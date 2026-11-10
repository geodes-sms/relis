"""Prepare a disposable baseline from a Git ref, never from the existing database.

Requires Python 3.9+, Git and Docker. Does not start application services or overwrite snapshots.
"""
import argparse
import io
import json
import re
import secrets
import shutil
import subprocess
import tarfile
import uuid
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
BASELINE_REF = '54084cc78429efbc0c05f7786110a33309b7cb1f'
HARNESS = [
    'relis_app/helpers/tests/helpers/curl_helper.php',
    'relis_app/helpers/tests/helpers/tests_helper.php',
    'relis_app/helpers/tests/api_ut_helper.php',
    'relis_app/controllers/test/Unit_test.php',
    'relis_sys/libraries/Unit_test.php',
]


def run(*args):
    return subprocess.check_output(args, cwd=ROOT, text=True, encoding='utf-8').strip()


def changed_source_paths(source_ref):
    changed = run('git', 'diff', '--name-only', '-z', source_ref).split('\0')
    added = run('git', 'ls-files', '--others', '--exclude-standard', '-z').split('\0')
    # Attribute-only EOL fixes can leave the normalized Git blob unchanged.
    # Overlay their actual checkout bytes too: the archived old ref may have
    # used different attributes (and Windows core.autocrlf conversion).
    tracked = subprocess.check_output(['git', 'ls-files', '-z'], cwd=ROOT)
    attributes = subprocess.run(['git', 'check-attr', '-z', '--stdin', 'eol'],
                                cwd=ROOT, input=tracked, capture_output=True, check=True)
    fields = attributes.stdout.decode('utf-8').split('\0')
    lf_paths = [fields[i] for i in range(0, len(fields) - 2, 3) if fields[i + 2] == 'lf']
    return sorted(set(p for p in changed + added + lf_paths if p))


def sync_candidate(name):
    if run('git', 'branch', '--show-current') != 'codex/modernize-php84-mariadb11':
        raise RuntimeError('Candidate synchronization requires the migration branch.')
    if not re.fullmatch(r'[a-z][a-z0-9-]{0,24}', name):
        raise ValueError('Invalid fixture name.')
    directory = ROOT / 'data/migration' / name
    metadata = json.loads((directory / 'baseline.json').read_text())
    if metadata.get('source_mode') != 'working-tree':
        raise RuntimeError('Reference snapshots are frozen; only candidates can be synchronized.')
    volume = json.loads(run('docker', 'volume', 'inspect', metadata['source_volume']))[0]
    if volume.get('Labels', {}).get('relis.migration.fixture') != metadata['fixture_id']:
        raise RuntimeError('Source volume identity does not match this fixture.')
    private = {'relis_app/config/database.php', 'relis_app/helpers/tests/helpers/cookies.txt'}
    private_prefixes = ('relis_app/config/development/', 'relis_app/config/dev/',
                        'cside/sessions/', 'cside/export_r/', 'cside/export_python/')
    payload = io.BytesIO()
    copied, removed = [], []
    # Include former overlays so reverting a local edit also restores the
    # candidate copy. A diff alone would leave the previous edit in its volume.
    paths = sorted(set(changed_source_paths(metadata['source_commit']) +
                       metadata.get('working_tree_overlays', []) +
                       metadata.get('synchronized_paths', [])))
    with tarfile.open(fileobj=payload, mode='w') as bundle:
        for relative in paths:
            if relative in private or relative.startswith(private_prefixes):
                continue
            target = (ROOT / relative).resolve()
            if not target.is_relative_to(ROOT):
                raise ValueError('Source path escapes the repository.')
            if target.is_file():
                bundle.add(target, arcname=relative, recursive=False)
                copied.append(relative)
            elif not target.exists():
                removed.append('/snapshot/' + relative)
    setup = ['docker', 'run', '--rm', '--network', 'none', '--mount',
             'type=volume,source=' + metadata['source_volume'] + ',target=/snapshot']
    subprocess.run(setup + ['-i', '--entrypoint', 'tar', metadata['images']['PHP']['id'],
                           '-xf', '-', '-C', '/snapshot'], input=payload.getvalue(), check=True)
    if removed:
        subprocess.run(setup + ['--entrypoint', 'rm', metadata['images']['PHP']['id'],
                               '-f', '--'] + removed, check=True)
    metadata['working_tree_overlays'] = copied
    metadata['synchronized_paths'] = copied + [p.removeprefix('/snapshot/') for p in removed]
    (directory / 'baseline.json').write_text(json.dumps(metadata, indent=2) + '\n')
    print('Synchronized %d files to candidate %s; reference fixtures remain frozen.' % (len(copied), name))


def prepare(name, port, source_ref, working_tree=False, php_image=None, db_image=None):
    branch = run('git', 'branch', '--show-current')
    if branch != 'codex/modernize-php84-mariadb11':
        raise RuntimeError('Run this preparation only on codex/modernize-php84-mariadb11.')
    if not re.fullmatch(r'[a-z][a-z0-9-]{0,24}', name) or not 1024 <= port <= 65535:
        raise ValueError('Use a short lowercase fixture name and an unprivileged port.')
    directory = ROOT / 'data' / 'migration' / name
    if directory.exists():
        raise FileExistsError('Snapshot already exists; use a new name to preserve its data.')
    commit = run('git', 'rev-parse', source_ref + '^{commit}')
    images = {}
    for key, image in [('PHP', php_image or 'deployment-relis-application'), ('DB', db_image or 'mariadb:11.4'),
                       ('TOMCAT', 'deployment-tomcat'), ('BIBLER', 'relis/bibler')]:
        metadata = json.loads(run('docker', 'image', 'inspect', image))[0]
        images[key] = {'id': metadata['Id'], 'repo_digests': metadata.get('RepoDigests', [])}
    source = directory / 'source'
    source.mkdir(parents=True)
    archive = subprocess.check_output(['git', 'archive', '--format=zip', commit], cwd=ROOT)
    with zipfile.ZipFile(io.BytesIO(archive)) as snapshot:
        for member in snapshot.infolist():
            target = (source / member.filename).resolve()
            if not target.is_relative_to(source.resolve()):
                raise ValueError('Unsafe archive path.')
        snapshot.extractall(source)
    working_overlays = []
    if working_tree:
        tracked_changes = changed_source_paths(commit)
        for relative in tracked_changes:
            if relative == 'relis_app/config/database.php':
                continue
            target = source / relative
            if (ROOT / relative).is_file():
                target.parent.mkdir(parents=True, exist_ok=True)
                shutil.copyfile(ROOT / relative, target)
            elif target.is_file():
                target.unlink()
            working_overlays.append(relative)
    # Apply identical test-only instrumentation to the original source baseline.
    for relative in HARNESS:
        shutil.copyfile(ROOT / relative, source / relative)
    shutil.copytree(ROOT / 'deployment' / 'migration', source / 'deployment' / 'migration',
                    dirs_exist_ok=True, ignore=shutil.ignore_patterns('__pycache__'))
    for relative in ['cside/sessions', 'cside/export_r', 'cside/export_python',
                     'relis_app/logs', 'relis_app/libraries/table_config/project/temp']:
        (source / relative).mkdir(parents=True, exist_ok=True)
    (directory / 'reports' / 'mail').mkdir(parents=True)
    (directory / 'reports' / 'cookies.txt').write_text('')
    fixture_id = str(uuid.uuid4())
    project = 'relis-migration-' + name
    source_volume = project + '_source'
    password = secrets.token_hex(24)
    (directory / 'guard.sql').write_text(
        "CREATE DATABASE relis_migration_guard;\n"
        "CREATE TABLE relis_migration_guard.fixture (id VARCHAR(36) PRIMARY KEY);\n"
        "INSERT INTO relis_migration_guard.fixture VALUES ('%s');\n" % fixture_id)
    database = (source / 'relis_app/config/database.example.php').read_text()
    database += "\n$db['default']['password'] = getenv('RELIS_TEST_DB_PASSWORD');\n"
    (source / 'relis_app/config/database.php').write_text(database, encoding='utf-8')
    config = source / 'relis_app/config/dev/config.php'
    config.parent.mkdir(parents=True, exist_ok=True)
    config.write_text("""<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config['base_url'] = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'relis.test') . '/';
$config['project_db_user'] = 'root';
$config['project_db_host'] = 'db';
$config['project_db_pass'] = getenv('RELIS_TEST_DB_PASSWORD');
$config['mail_protocol'] = 'smtp';
$config['mail_host'] = 'mail';
$config['mail_port'] = 1025;
$config['mail_user'] = '';
$config['mail_password'] = '';
$config['mail_from'] = 'relis@example.invalid';
$config['mail_reply_to'] = 'relis@example.invalid';
$config['log_threshold'] = 1;
""", encoding='utf-8')
    canonical_config = source / 'relis_app/config/development/config.php'
    canonical_config.parent.mkdir(parents=True, exist_ok=True)
    shutil.copyfile(config, canonical_config)
    environment = {
        'COMPOSE_PROJECT_NAME': project,
        'MIGRATION_DIRECTORY': directory.as_posix(),
        'MIGRATION_SOURCE': source.as_posix(),
        'MIGRATION_SOURCE_VOLUME': source_volume,
        'MIGRATION_ID': fixture_id,
        'MIGRATION_HTTP_PORT': str(port),
        'MIGRATION_DB_PASSWORD': password,
        **{'MIGRATION_' + key + '_IMAGE': value['id'] for key, value in images.items()},
    }
    exists = subprocess.run(['docker', 'volume', 'inspect', source_volume],
                            capture_output=True, cwd=ROOT)
    if exists.returncode == 0:
        raise FileExistsError('Source volume already exists; refusing to reuse it.')
    run('docker', 'volume', 'create', '--label', 'com.docker.compose.project=' + project,
        '--label', 'com.docker.compose.volume=source', '--label',
        'relis.migration.fixture=' + fixture_id, source_volume)
    # Use Linux storage for case-sensitive files and fast PHP bootstrap on Windows.
    # This setup container only extracts our prepared snapshot; it runs no application.
    payload = io.BytesIO()
    with tarfile.open(fileobj=payload, mode='w') as bundle:
        bundle.add(source, arcname='.')
    subprocess.run(['docker', 'run', '--rm', '--network', 'none', '-i', '--mount',
                    'type=volume,source=' + source_volume + ',target=/snapshot',
                    '--entrypoint', 'tar', images['PHP']['id'], '-xf', '-', '-C', '/snapshot'],
                   input=payload.getvalue(), cwd=ROOT, check=True)
    (directory / '.env').write_text(''.join('%s=%s\n' % item for item in environment.items()))
    (directory / 'baseline.json').write_text(json.dumps({
        'source_commit': commit, 'branch': branch,
        'source_mode': 'working-tree' if working_tree else 'reference',
        'working_tree_overlays': working_overlays,
        'fixture_id': fixture_id, 'images': images, 'source_volume': source_volume,
        'harness_overlays': HARNESS,
        'php_version': run('docker', 'run', '--rm', '--network', 'none', '--entrypoint',
                           'php', images['PHP']['id'], '-d', 'xdebug.remote_enable=0',
                           '-d', 'xdebug.mode=off', '-r', 'echo PHP_VERSION;'),
        'config_overlays': ['database.example.php + private fixture password',
                            'dev/development config: internal SMTP, request host, DB password, error logging',
                            'no-debug.ini: disable debugger connections only in migration containers'],
    }, indent=2) + '\n')
    print('Prepared:', directory)
    print('Isolated project:', environment['COMPOSE_PROJECT_NAME'])
    print('HTTP: http://127.0.0.1:%d/' % port)
    print('Install the locked dependencies before starting PHP; see migration/README.md.')


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--name', default='baseline')
    parser.add_argument('--port', type=int, default=18083)
    parser.add_argument('--ref', default=BASELINE_REF, help='Source commit/ref; defaults to the assessed migration base.')
    parser.add_argument('--working-tree', action='store_true', help='Overlay local source changes for a candidate; never changes the reference fixture.')
    parser.add_argument('--php-image', help='Candidate PHP runtime image; resolved and recorded by immutable ID.')
    parser.add_argument('--db-image', help='Candidate database image; resolved and recorded by immutable ID.')
    parser.add_argument('--sync', action='store_true', help='Synchronize source changes to an existing candidate; reference fixtures are rejected.')
    args = parser.parse_args()
    if args.sync:
        sync_candidate(args.name)
    else:
        prepare(args.name, args.port, args.ref, args.working_tree, args.php_image, args.db_image)
