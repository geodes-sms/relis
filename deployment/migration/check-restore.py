"""Compare the same fixture dump restored into old/new disposable MariaDB servers.

Uses no host data-directory mounts, published ports or application network. Containers
have unique ownership labels and temporary in-memory data directories. This is not a
production-data or in-place-upgrade rehearsal.
"""
import argparse
import json
import re
import secrets
import subprocess
import time
import uuid
from pathlib import Path

from prepare import ROOT, run


def command(args, **kwargs):
    result = subprocess.run(args, cwd=ROOT, capture_output=True, **kwargs)
    if result.returncode:
        # Do not echo arguments containing the private fixture password.
        raise RuntimeError('Restore command failed: ' + result.stderr.decode(errors='replace')[-2000:])
    return result.stdout


def check_restore(directory, target_image):
    if run('git', 'branch', '--show-current') != 'codex/modernize-php84-mariadb11':
        raise RuntimeError('Run only on the migration branch.')
    directory = directory.resolve()
    if not directory.is_relative_to(ROOT / 'data/migration'):
        raise ValueError('Use an isolated migration fixture directory.')
    metadata = json.loads((directory / 'baseline.json').read_text())
    dump = (directory / 'fixture-bootstrap.sql').read_bytes()
    schemas = sorted(set(re.findall(rb'^CREATE DATABASE[^\n]*?`([^`]+)`', dump, re.M)))
    schemas = [name.decode() for name in schemas]
    if not schemas or any(not re.fullmatch(r'[a-zA-Z0-9_]+', name) for name in schemas):
        raise ValueError('Expected explicit application schema names in the fixture dump.')
    if 'relis_migration_guard' not in schemas or any(name in ('mysql', 'sys') for name in schemas):
        raise ValueError('Requires the fixture guard and excludes server system databases.')
    target = json.loads(run('docker', 'image', 'inspect', target_image))[0]
    images = [metadata['images']['DB']['id'], target['Id']]
    snapshots = []
    for image in images:
        identity = str(uuid.uuid4())
        container = 'relis-migration-restore-' + identity[:12]
        password = secrets.token_hex(24)
        command(['docker', 'run', '--rm', '-d', '--network', 'none', '--name', container,
                 '--label', 'relis.migration.restore=' + identity, '--tmpfs', '/var/lib/mysql',
                 '-e', 'MARIADB_ROOT_PASSWORD=' + password, image])

        def query(sql, check=True):
            result = subprocess.run(['docker', 'exec', '-i', '-e', 'MYSQL_PWD=' + password,
                                     container, 'mariadb', '--batch', '--skip-column-names'],
                                    input=sql.encode(), capture_output=True)
            if check and result.returncode:
                raise RuntimeError('Fixture SQL failed: ' + result.stderr.decode(errors='replace')[-1500:])
            return result.stdout.decode().strip() if result.returncode == 0 else None

        try:
            deadline = time.monotonic() + 120
            while query('SELECT 1', check=False) != '1':
                if time.monotonic() >= deadline:
                    raise TimeoutError('Disposable MariaDB startup exceeded 120 seconds.')
                time.sleep(1)
            command(['docker', 'exec', '-i', '-e', 'MYSQL_PWD=' + password, container, 'mariadb'], input=dump)
            if query('SELECT id FROM relis_migration_guard.fixture') != metadata['fixture_id']:
                raise RuntimeError('Restored fixture identity mismatch.')
            selection = ','.join("'%s'" % name for name in schemas)
            tables = query('SELECT TABLE_SCHEMA,TABLE_NAME,ENGINE,TABLE_COLLATION,TABLE_TYPE FROM information_schema.TABLES '
                           'WHERE TABLE_SCHEMA IN (' + selection + ') ORDER BY TABLE_SCHEMA,TABLE_NAME')
            counts, checksums = {}, {}
            for line in tables.splitlines():
                schema, table, _, _, kind = line.split('\t')
                quoted = '`' + schema + '`.`' + table.replace('`', '``') + '`'
                counts[schema + '.' + table] = int(query('SELECT COUNT(*) FROM ' + quoted))
                if kind == 'BASE TABLE':
                    checksums[schema + '.' + table] = query('CHECKSUM TABLE ' + quoted + ' EXTENDED').split('\t')[-1]
            routines = query('SELECT ROUTINE_SCHEMA,ROUTINE_NAME,ROUTINE_TYPE,DEFINER,SQL_MODE,'
                             'CHARACTER_SET_CLIENT,COLLATION_CONNECTION,DATABASE_COLLATION,ROUTINE_DEFINITION '
                             'FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA IN (' + selection + ') '
                             'ORDER BY ROUTINE_SCHEMA,ROUTINE_NAME')
            columns = query('SELECT TABLE_SCHEMA,TABLE_NAME,COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,'
                            'COLUMN_DEFAULT,CHARACTER_SET_NAME,COLLATION_NAME,EXTRA '
                            'FROM information_schema.COLUMNS WHERE TABLE_SCHEMA IN (' + selection + ') '
                            'ORDER BY TABLE_SCHEMA,TABLE_NAME,ORDINAL_POSITION')
            objects = query('SELECT TRIGGER_SCHEMA,TRIGGER_NAME,ACTION_STATEMENT,DEFINER FROM information_schema.TRIGGERS '
                            'WHERE TRIGGER_SCHEMA IN (' + selection + ') ORDER BY TRIGGER_SCHEMA,TRIGGER_NAME')
            views = query('SELECT TABLE_SCHEMA,TABLE_NAME,VIEW_DEFINITION,CHECK_OPTION,IS_UPDATABLE,DEFINER,SECURITY_TYPE '
                          'FROM information_schema.VIEWS WHERE TABLE_SCHEMA IN (' + selection + ') ORDER BY TABLE_SCHEMA,TABLE_NAME')
            parameters = query('SELECT SPECIFIC_SCHEMA,SPECIFIC_NAME,ORDINAL_POSITION,PARAMETER_MODE,PARAMETER_NAME,DTD_IDENTIFIER '
                               'FROM information_schema.PARAMETERS WHERE SPECIFIC_SCHEMA IN (' + selection + ') '
                               'ORDER BY SPECIFIC_SCHEMA,SPECIFIC_NAME,ORDINAL_POSITION')
            indexes = query('SELECT TABLE_SCHEMA,TABLE_NAME,INDEX_NAME,NON_UNIQUE,SEQ_IN_INDEX,COLUMN_NAME,SUB_PART,INDEX_TYPE '
                            'FROM information_schema.STATISTICS WHERE TABLE_SCHEMA IN (' + selection + ') '
                            'ORDER BY TABLE_SCHEMA,TABLE_NAME,INDEX_NAME,SEQ_IN_INDEX')
            events = query('SELECT EVENT_SCHEMA,EVENT_NAME,DEFINER,EVENT_DEFINITION,SQL_MODE,EVENT_TYPE,INTERVAL_VALUE,INTERVAL_FIELD,STATUS '
                           'FROM information_schema.EVENTS WHERE EVENT_SCHEMA IN (' + selection + ') ORDER BY EVENT_SCHEMA,EVENT_NAME')
            defaults = query('SELECT SCHEMA_NAME,DEFAULT_CHARACTER_SET_NAME,DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA '
                             'WHERE SCHEMA_NAME IN (' + selection + ') ORDER BY SCHEMA_NAME')
            snapshots.append({'image': image, 'version': query('SELECT VERSION()'), 'tables': tables,
                              'row_counts': counts, 'checksums': checksums, 'routines': routines,
                              'columns': columns, 'triggers': objects, 'views': views, 'parameters': parameters,
                              'indexes': indexes, 'events': events, 'schema_defaults': defaults})
        finally:
            inspection = json.loads(run('docker', 'inspect', container))[0]
            if inspection['Config']['Labels'].get('relis.migration.restore') != identity:
                raise RuntimeError('Refusing cleanup: disposable-container identity mismatch.')
            command(['docker', 'stop', '--time', '15', container])
    matches = {key: snapshots[0][key] == snapshots[1][key]
               for key in ('tables', 'row_counts', 'checksums', 'routines', 'columns', 'triggers',
                           'views', 'parameters', 'indexes', 'events', 'schema_defaults')}
    report = {'status': 'passed' if all(matches.values()) else 'failed',
              'scope': 'same fixture logical restore into fresh servers; no production or existing data volume',
              'fixture_id': metadata['fixture_id'], 'schemas': schemas,
              'images': images, 'versions': [s['version'] for s in snapshots],
              'table_count': len(snapshots[0]['checksums']),
              'view_count': len(snapshots[0]['views'].splitlines()),
              'routine_count': len(snapshots[0]['routines'].splitlines()), 'matches': matches}
    output = directory / 'reports' / ('restore-' + snapshots[1]['version'].split('-')[0] + '.json')
    output.write_text(json.dumps(report, indent=2) + '\n')
    # Retain details for investigating a mismatch without logging application data.
    output.with_suffix('.details.json').write_text(json.dumps(snapshots, indent=2) + '\n')
    print(json.dumps(report, indent=2))
    return all(matches.values())


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('fixture', type=Path)
    parser.add_argument('--target-image', required=True)
    args = parser.parse_args()
    raise SystemExit(0 if check_restore(args.fixture, args.target_image) else 1)
