# Isolated migration baseline

Run from the repository root, on `codex/modernize-php84-mariadb11`. This environment
uses the assessed Git base (`54084cc`) plus the listed test instrumentation, a fresh seed database,
private generated config/session/export files, a local SMTP sink and an internal-only
Docker network. It never mounts the existing `data/db`, editor workspace or checkout.
PHP runs from its own Linux source volume seeded by preparation; the host snapshot
is retained for inspection. This avoids Windows bind-mount overhead and exercises Linux
case sensitivity. Runtime-generated source/config changes live in that private volume.
Only the web port is published, bound to 127.0.0.1. Existing ReLiS containers stay running.
The migration-only INI disables debugger connection attempts in the captured development
image. Its Xdebug 2 remote autostart otherwise delays CLI execution on the internal network.

Python 3.9+, Git, a Linux Docker engine and the existing local images are required.
`prepare.py` captures exact image IDs rather than silently rebuilding old tags. On this
machine the cached PHP image reports **7.3.30**, despite the Dockerfile declaring 8.1.31.
Its exact original build cannot be reproduced from the current Dockerfile; retain the
captured image while this offline reference is needed. Use `--ref` to select another
source revision explicitly; new migration commits do not silently change the baseline.
The SMTP sink currently uses the captured editor image's Python 3.7 standard library;
replace that implementation when upgrading the Python runtime.

```powershell
python deployment/migration/prepare.py --name baseline --port 18083
$fixture = Join-Path (Get-Location) 'data/migration/baseline/.env'
$compose = 'deployment/docker-compose.migration.yml'

# Install the existing lock into the disposable snapshot. This one-off container
# can download packages; it starts no application, runs no scripts or plugins,
# and has no database or source-workspace mounts beyond this disposable snapshot.
$metadata = Get-Content 'data/migration/baseline/baseline.json' -Raw | ConvertFrom-Json
docker run --rm --network bridge --mount "type=volume,source=$($metadata.source_volume),target=/u/relis/public_html" --workdir /u/relis/public_html --entrypoint composer $metadata.images.PHP.id install --no-dev --prefer-dist --no-interaction --no-scripts --no-plugins

docker compose --env-file $fixture -f $compose up -d --wait --wait-timeout 180
docker compose --env-file $fixture -f $compose ps
docker compose --env-file $fixture -f $compose exec -T relis-application php deployment/migration/check-harness.php
docker compose --env-file $fixture -f $compose exec -T relis-application php deployment/migration/run.php bootstrap
docker compose --env-file $fixture -f $compose exec -T relis-application php deployment/migration/run.php test

# Stop only this fixture. No -v: preserve fixture data and evidence for review.
docker compose --env-file $fixture -f $compose down
```

The migration network deliberately blocks external egress. The one-off dependency install
above has external access; running application services do not. Do not connect application
services to an external network or point tests at the ordinary development URL.

Before executing tests, inspect resolved Compose mounts and running container labels,
confirm that the DB volume belongs to this migration project, and check the network is
internal. `run.php` additionally checks the unique fixture ID stored in the freshly seeded
database before any bootstrap or destructive test action. A new fixture name gets separate
source/storage; preparation refuses to overwrite an existing fixture.

Results, captured mail and private generated credentials stay under ignored
`data/migration/<name>/`. `baseline.json` records source/image identities and overlays.
`reports/test-results.json` and `test-report.html` come from one suite execution;
zero assertions, malformed responses and assertion failures produce nonzero exit codes.
The snapshot uses the internal `relis.test` network alias and a separate cookie jar
instead of fixed port 8083. The dotted alias is necessary because the baseline's old
libcurl does not reload cookies for a single-label service hostname. Original test
assertions and suite order are preserved.

## PHP 8.4 candidate

Keep the completed `baseline-linux` fixture frozen. Build a production PHP runtime
(no Xdebug), then create a separate candidate with its own name, port and storage:

```powershell
docker build --build-arg ENV=production -t relis-migration/php84:local deployment
python deployment/migration/prepare.py --name php84 --port 18085 --working-tree --php-image relis-migration/php84:local
$fixture = 'data/migration/php84/.env'
$metadata = Get-Content 'data/migration/php84/baseline.json' -Raw | ConvertFrom-Json
docker run --rm --network bridge --mount "type=volume,source=$($metadata.source_volume),target=/u/relis/public_html" --workdir /u/relis/public_html --entrypoint composer $metadata.images.PHP.id install --no-dev --prefer-dist --no-interaction --no-scripts --no-plugins
docker compose --env-file $fixture -f deployment/docker-compose.migration.yml up -d --wait --wait-timeout 180
docker compose --env-file $fixture -f deployment/docker-compose.migration.yml exec -T relis-application php deployment/migration/lint.php
docker compose --env-file $fixture -f deployment/docker-compose.migration.yml exec -T relis-application php deployment/migration/check-harness.php
docker compose --env-file $fixture -f deployment/docker-compose.migration.yml exec -T relis-application php deployment/migration/run.php bootstrap
docker compose --env-file $fixture -f deployment/docker-compose.migration.yml exec -T relis-application php deployment/migration/check-runtime.php
docker compose --env-file $fixture -f deployment/docker-compose.migration.yml exec -T relis-application php deployment/migration/run.php test
```

`--db-image` selects another previously pulled MariaDB image for a **new** fixture.
It does not upgrade an existing data volume. The first PHP candidate deliberately
retains MariaDB 11.4.2 to distinguish PHP regressions from database changes.

After editing source, synchronize only the candidate:

```powershell
python deployment/migration/prepare.py --name php84 --sync
```

Synchronization verifies the migration branch, candidate metadata and source-volume
identity. It refuses reference fixtures and preserves generated database credentials,
private environment overlays, runtime session/export files and the installed vendor
tree. Changes are compared with the fixture's recorded source commit, so committing
changes on the migration branch does not omit them. Dependency or image changes
need a fresh locked install or a new fixture, respectively; source sync does neither.
Paths explicitly marked `eol=lf` in the current `.gitattributes` are also overlaid
from the checkout. This preserves reviewed export fixture bytes when archiving an
older source ref whose attributes differed, even if Git's normalized blob is unchanged.

`check-runtime.php` checks real HTTP cookies and strict session-ID rejection, then
checks the CI mysqli driver's handling of working/lost/reinitialized connections in
both mysqli reporting modes. It kills only connections created by that script,
after verifying the fixture marker. It never terminates application connections.
Its 24 checks also cover model DB delegation, pagination and both legacy exclusion
controllers, including missing records and distinct exclusion/paper identifiers.
`lint.php` checks syntax and compile-time diagnostics without executing source files.

Compare captured reports with `compare-results.py REFERENCE_JSON CANDIDATE_JSON`.
It requires every original assertion occurrence in the same relative order, allows
additional regression assertions, lists newly failing/resolved assertions and records
diagnostics. Missing, renamed or reordered original assertions fail comparison. Its
exit status stays nonzero while the candidate suite fails, including inherited
failures and failures in added assertions; a matching failure count is insufficient.
The candidate report must include zero logged PHP diagnostics and zero logged SQL
query errors. Missing evidence for either count also fails comparison.

### Focused repair runs

Run one group at a time against a prepared, bootstrapped candidate. These groups
mutate the same disposable databases and must not run concurrently. For example:

```powershell
docker compose --env-file data/migration/step1/.env -f deployment/docker-compose.migration.yml exec -T relis-application php deployment/migration/run.php test screening
docker compose --env-file data/migration/step1/.env -f deployment/docker-compose.migration.yml exec -T relis-application php deployment/migration/run.php test reporting
```

Allowed groups are `user`, `project`, `paper`, `screening`, `quality_assessment`,
`data_extraction`, `reporting`, `element`, `home`, `install`, `admin`, `config`,
`manage`, `manager`, `relismanager`, `api`, `apiquery`, `op` and
`inclusion_mode_conflict`. Omitting the group (or using `all`) runs the entire
suite in its original order. Focused execution requires the migration JSON endpoint
and the fixture identity guard; it is unavailable through the ordinary HTML runner.

Focused report names start with the group, for example `screening-test-results.json`
and `screening-runtime-diagnostics.json`, and preserve the full-suite report.
`http-trace.jsonl` records response chains without POST bodies or cookies. Strict
export comparisons retain actual/expected bytes and difference metadata under
`export-comparisons/` before cleanup. All remain private under the fixture's reports.

Finish a repair batch with a fresh fixture using the selected PHP and MariaDB image
IDs, a locked Composer install, bootstrap, runtime/harness/lint checks and one full
suite run. Compare that full report with the frozen `baseline-linux` report:

```powershell
python deployment/migration/compare-results.py data/migration/baseline-linux/reports/test-results.json data/migration/step1/reports/test-results.json --output data/migration/step1/reports/comparison.json
```

The original runtime checkpoint is retained separately under
`data/migration/php84-db114/reports/runtime-checkpoint/`; do not overwrite it or
synchronize the frozen reference. See section 16 of `deployment/MIGRATION_PLAN.md`
for the Step 1 results and limits.

For the captured, guarded bootstrap dump, test a fresh logical restore independently
of running applications:

To create that dump for a new reference, run `run.php bootstrap` first and capture
it **before** starting the full suite, while no requests are changing the fixture.
The following PowerShell 7.4+ commands preserve native output bytes. Use the same
verified `$fixture` environment file; these are the three schemas of the fresh
bootstrap fixture, not a production backup inventory:

```powershell
$backup = 'data/migration/baseline/fixture-bootstrap.sql'
if (Test-Path -LiteralPath $backup) { throw 'Preserve the existing fixture backup; use a new fixture.' }
docker compose --env-file $fixture -f deployment/docker-compose.migration.yml exec -T db sh -c 'exec mariadb-dump --user=root --password="$MARIADB_ROOT_PASSWORD" --single-transaction --routines --triggers --events --hex-blob --databases relis_db relis_dev_correct_demo_relis relis_migration_guard' > $backup
if ($LASTEXITCODE -ne 0) { throw 'Backup failed; do not use the incomplete output.' }
docker compose --env-file $fixture -f deployment/docker-compose.migration.yml exec -T relis-application tar -cf - relis_app/config/database.php relis_app/libraries/table_config/project > data/migration/baseline/fixture-bootstrap-config.tar
if ($LASTEXITCODE -ne 0) { throw 'Configuration backup failed.' }
```

Then select the corresponding fixture directory for comparison (the completed
reference in this session is named `baseline-linux`):

```powershell
docker pull mariadb:11.4.13
python deployment/migration/check-restore.py data/migration/baseline-linux --target-image mariadb:11.4.13
```

This restores the same fixture dump into the recorded old image and selected target
image, then compares tables, columns/collations, row counts, checksums, procedure
bodies/parameters/definers/SQL modes, views, indexes, triggers and events. Both servers have no network or host data mount
and use temporary data directories; cleanup verifies their unique ownership labels.
It does not migrate the ordinary development database or establish production recovery.

Composer validation and platform checks can run inside the application container.
Auditing needs current advisory data, so run that command in a separate container
with internet access, a read-only candidate source volume and no database connection:

```powershell
$metadata = Get-Content 'data/migration/php84-db114/baseline.json' -Raw | ConvertFrom-Json
docker run --rm --network bridge --mount "type=volume,source=$($metadata.source_volume),target=/u/relis/public_html,readonly" --workdir /u/relis/public_html --entrypoint composer $metadata.images.PHP.id --no-interaction --no-plugins audit --locked
```

The runner also captures `application-log-delta.txt` and `runtime-diagnostics.json`
for the request interval. An empty outer test response diagnostic does not prove
that individual application requests emitted no warnings. These private files
include expected-error cases and must be reviewed separately from assertion totals.
The report's `logged_query_error_count` must also be zero; HTTP 500 responses from
unasserted setup requests previously left malformed SQL visible only in these logs.
