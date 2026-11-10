# ReLiS stack assessment and migration plan

Assessment date: 2026-11-10. Base: `main` at `54084cc78429efbc0c05f7786110a33309b7cb1f`.
Working branch: `codex/modernize-php84-mariadb11`.

The owner approved local implementation on 2026-11-10. Work is proceeding on the dedicated migration branch. Sections 11 onward record implementation evidence and supersede the initial runtime limitations below. Section 16 records the Step 1 repairs and verification. No commit, push or production deployment has been performed; production deployment remains a separate authorized action.

## 1. Target and scope

Migrate incrementally to **patched PHP 8.4.x with Apache**, retain **CodeIgniter 3**, and standardize on **a current MariaDB 11.4 LTS maintenance release**. Preserve review data, project generation, routes, permissions, imports and exports. Keep runtime compatibility fixes separate from security changes that alter behavior.

The original PHP 8.4.7 target was corrected with the owner's agreement during this assessment. PHP **8.4.27** is the current published 8.4 release checked on this date and contains the PHP2Shell fixes, CVE-2026-109618 and CVE-2026-109619. Use that release or a newer verified stable 8.4 security patch when building. Pin the exact image variant and digest after checking availability and testing it. Apache/mod_php is directly relevant to these advisories. [PHP Foundation announcement](https://thephp.foundation/blog/2026/10/21/todays-php-security-release-and-how-were-strengthening-php-security/)

Also apply the PHP project's current recommendation to disable `session.upload_progress.enabled` in startup configuration pending the follow-up fix for CVE-2026-115560; recheck this advisory when selecting the final patch. Restart every PHP-serving container and verify the effective HTTP configuration. Suppressing warnings or changing application code does not establish that the engine is patched. [PHP security notices, October 2026](https://www.php.net/archive/2026.php)

Both Compose files already specify `mariadb:11.4`; this may be a maintenance update rather than a major database upgrade. The local `data/db/mysql_upgrade_info` marker says **11.4.2-MariaDB**, but this is historical filesystem evidence, not a running-server query or evidence of production's version. Recommend staying on 11.4: community maintenance runs to **29 May 2029**, compared with **4 June 2028** for 11.8. MariaDB 11.4.13 is a verified published maintenance candidate; recheck the latest stable 11.4 patch before pinning it. [Maintenance policy](https://mariadb.org/about/#maintenance-policy), [11.4.13 release notes](https://mariadb.com/docs/release-notes/community-server/11.4/11.4.13)

PHP 8.5 and CodeIgniter 4 are outside this migration. A framework rewrite, a frontend redesign, a database charset conversion and a new queue/cache platform are unnecessary for the initial compatibility objective. Security findings that affect reopening still need explicit resolution or a documented deployment decision.


The owner clarified that `deployment/tomcat/relis.war` and `relis/bibler` stay in their present form. Preserve those services and make necessary integration changes in this repository; locating external source repositories or rebuilding their applications is not a prerequisite for the PHP/MariaDB work. Existing dependency exposure remains recorded for deployment access controls and the reopening decision.


## 2. Confirmed architecture and compatibility inventory

The tracked repository contains 1,836 files, including 477 PHP files. ReLiS is a server-rendered MVC application with supporting services, rather than a standalone PHP site. Its application areas include user/project administration, papers, screening, quality assessment, data extraction, reporting and generated review configuration.

| Component | Confirmed current state and evidence | Target / required work | Verification |
| --- | --- | --- | --- |
| PHP / operating system / web SAPI | Dockerfile declares `php:8.1.31-apache`, but the running cached image is **PHP 7.3.30**, Apache **2.4.38**, Debian **10/buster**, Xdebug **2.8.0**. This runtime mismatch was confirmed in phase 1. | Patched PHP 8.4 Apache image on a maintained Debian variant; pin digest and capture package inventory. | CLI and Apache evidence, `apache2ctl -S/-M`, OS/package inventory; include every PHP migration guide crossed from 7.3. |
| Native PHP extensions | Dockerfile explicitly installs `intl`, `mysqli`, `pdo`, `pdo_mysql`, `zip`; development installs Xdebug 3.3.0. Application uses cURL, ZIP and mbstring; CI uploads/images may require fileinfo/GD depending on enabled paths. | Declare actual extension requirements. Check `curl`, `mbstring`, `fileinfo`, `ctype`, `iconv`, `session`, `openssl`, XML/DOM and optional image processing in the built image; a shell `curl` package does not provide `ext-curl`. | `php -m`, extension checks through Apache, upload/image/import/export tests. |
| PHP configuration | Three large INI copies under `deployment/php/conf`; Apache/CLI copies are placed in nested `conf.d` subdirectories. Deprecated settings, `E_STRICT`, and a multiline `disable_functions` value require validation. | Small explicit shared settings plus intentional SAPI differences; verify scan paths because nested folders do not automatically select settings by SAPI. | `php --ini`, startup stderr, private HTTP `php_ini_scanned_files()` and selected settings. |
| CodeIgniter | `relis_sys/core/CodeIgniter.php`: **3.1.13**, vendored directly; Composer does not install it as a dependency. | Retain 3.1.13 baseline plus a small, documented set of PHP 8.4 compatibility patches. Do not replace `relis_sys` wholesale. | Upstream comparison, framework smoke tests, existing ReLiS suite under PHP 8.4 with deprecations logged. |
| Framework modifications | Compared every `relis_sys` file with upstream tag 3.1.13 after CRLF normalization: exactly four modified files, no added/missing files. See section 3. | Preserve all intentional extensions and custom test behavior. | Review patch diff and test runner semantics before/after changes. |
| Composer production dependencies | Lockfile: Twig **3.11.0**, Symfony deprecation-contracts **2.5.3**, polyfills ctype/mbstring/php80/php81 **1.30.0**. Manifest incorrectly retains framework boilerplate and PHP `>=5.2.4`. | Update Twig and related dependencies deliberately within compatible stable versions; correct PHP/extension requirements and project metadata. | Composer validation, targeted update, audit, platform checks and generated Python export fixtures. |
| Installed vendor tree | Ignored local `vendor` differs from lock: Twig **3.10.3**, fewer installed polyfills. Framework's Composer autoload is disabled; `Reporting.php` explicitly includes `vendor/autoload.php`. | Reproduce lock installation in an isolated source copy; never treat this local vendor directory as the release artifact. | Compare `composer show` with `composer show --locked`; clean production install. |
| Composer development tools | `mikey179/vfsstream` **1.1.0**. No PHPUnit configuration or PHPUnit dependency found. | Establish actual usage before updating/removing it. Keep any compatibility scanner isolated from production dependencies. | Search dynamic test loading; execute existing suite and any selected scanner. |
| Debugging | Dockerfile specifies Xdebug **3.3.0**, but the captured runtime has **2.8.0** with remote autostart/connect-back enabled. | Disable debugger connections in the isolated reference; pin a supported PHP 8.4-compatible debugger for development only. Xdebug 3.3 does not support PHP 8.4. [Compatibility matrix](https://xdebug.org/docs/compat) | Development startup/debug test; verify no Xdebug in production. |
| MariaDB / driver | Both Compose files use floating patch tag `mariadb:11.4`; CI uses `mysqli`, nonpersistent connections, `stricton=FALSE`, query caching disabled. | Pin tested 11.4 patch/digest. Identify production source version before choosing upgrade procedure. Keep mysqli and current schema semantics initially. | Server version/settings queries; restore, routine and transaction tests. |
| Database model | Central `relis_db` plus one database/configuration group per review. Application creates/drops databases, generates routines and appends `relis_app/config/database.php`. | Preserve every project database and its generated PHP/configuration artifacts. Design grants for project provisioning, not merely CRUD. | Create/edit/delete a disposable project; switch projects; verify grants, routine counts and cross-project isolation. |
| Database seed | `deployment/db/initial_db.sql` originated from MariaDB **10.1.44**; 11 InnoDB tables and 60 procedures. Nine table declarations use latin1, two use utf8; database default is utf8mb4. Routines have `root@localhost` definers. | Test seed and production restore separately. Preserve encodings first; repair only proven incompatibilities, with explicit definer/grant mapping. | Fresh installation plus restored representative data; multilingual/BibTeX round trips and routine execution. |
| Java editor | Dockerfile: Tomcat **8.5.51**, JDK 8 AdoptOpenJDK; ~59.5 MB prebuilt WAR containing Eclipse/OSGi/DSLForge/Xtext and old jars including Log4j 1.2.15. Runtime logs also reveal **embedded Apache Derby 10.11.1.1** for editor metadata beneath the workspace; back it up consistently with editor files. Main editor build source is absent. | Retain the present WAR/Tomcat packaging as requested. Review deployed access boundaries and make necessary integration changes here. Any later runtime rebuild needs independent editor and workspace validation. | Open/save/reconfigure a DSL project, inspect generated PHP, audit WAR dependencies, test candidate Java and workspace/Derby recovery. |
| Editor file API | Python 3 from the Tomcat image's OS; unpinned FastAPI/Uvicorn installed with pip. `app.py` lists projects and returns files, on port 8181. | Pin Python dependencies and runtime; validate paths, authentication and access boundaries. | API contract, malformed path/traversal, unauthorized access and missing-file tests. |
| BibTeX service | `relis/bibler:latest`; source/build absent. Captured image runs Python **3.9.14**, FastAPI **0.85.0**, Pydantic **1.9.2**, Uvicorn **0.18.3**, on port 8000. | Record the present image digest, retain its packaging and verify PHP integration and network access boundaries. | Import/export BibTeX, Unicode, duplicates, malformed input and service-failure tests. |
| Proxy / administration | Production: unpinned Nginx; development: unpinned phpMyAdmin. Nginx proxies PHP, editor and file API. | Pin maintained releases, restrict phpMyAdmin to local/admin access, establish actual TLS termination. | Proxy routes, HTTPS/cookies, access restrictions, startup and security scan. |
| Browser stack | Manually copied assets: jQuery **2.2.3**, Bootstrap **3.3.6**, DataTables **1.10.11**, Select2 **4.0.2**, Switchery **0.8.1**, iCheck **1.0.2**, NProgress **0.2.0**, plus Font Awesome, PNotify, date/color pickers, FastClick and gauge.js. Some views additionally load CDN jQuery 3.2.1 slim/3.6.0, Bootstrap 4.0.0 and Popper 1.12.9. | Inventory actually loaded assets per page and audit supported replacements; make narrowly scoped security updates. Do not introduce a bundler or broad UI upgrade as a PHP prerequisite. | Browser login/forms, data tables, select widgets, dialogs, charts and downloads; console/network errors. |
| Authentication / authorization | Custom `Bm_lib`/`User_lib`, roles and per-project membership; MD5 password storage and a database credential-check procedure. Public-controller allowlist includes `unit_test`, `api`, `apiquery` and `user`. | Preserve role semantics during compatibility work; separately fix exposed operational routes and unsafe credential/query handling. See section 5 for password migration. | Login/reset/registration plus positive and negative role/project/API tests. |
| Sessions / cache / workers | Effective session driver is files (later assignment overrides database); path `cside/sessions` beneath document root, 7,200-second expiry. PHP GC probability 0; CI logging threshold 0. No active Redis/queue/scheduler setup found. | Private session storage with known cleanup and locking; preserve file sessions initially if deployment shares storage correctly. Confirm host-managed jobs separately. | Login/session regeneration/expiry, concurrent requests and multiple replicas, logging and cleanup tests. |
| Uploads / exports | CI uploads/images; BibTeX/CSV import/export, ZIP exports; generated R and Python analysis artifacts. Twig renders Python templates. | Preserve file formats, permissions and protected downloads. Determine actual image backend before requiring GD. R/Python artifact generation is distinct from executing analysis on the web server. | Existing fixtures plus encoding/CSV edge cases and generated artifact validation. |
| Exported Python environment | Templates pin matplotlib 3.8.2, numpy 1.24.1, pandas 1.5.3, scipy 1.11.4, seaborn 0.12.2, statsmodels 0.14.0, tabulate 0.9.0. | Check generation first; validate downloadable environment separately without assuming those packages run inside PHP/Tomcat. | Generate ZIP, inspect expected contents and run a representative exported analysis in an isolated environment. |
| External integrations | SMTP through CI libraries and a copied `TestEmail.php`; Google reCAPTCHA; remote/CDN browser assets. Hardcoded credential-like material exists in controller source. | Environment/private configuration, local mail capture and test stubs. Verify whether any committed credential remains active and rotate through the owner. | Captured registration/reset/email messages; CAPTCHA success/failure; blocked real outbound side effects during tests. |
| CI / tests | GitHub Actions `relisUnitTest.yml`, PRs to `main`; CodeIgniter's customized unit library plus HTTP/database integration helpers. 19 top-level suite invocations, ordered and stateful. | Retain these tests, make execution isolated and result reporting trustworthy. No separate browser/performance/static-analysis suite was found. | Reproduce existing endpoint and report, count actual assertions; add focused regression coverage. |
| Deployment / persistence | Development Compose; production file documents Swarm, three PHP replicas and three Bibler replicas. Bind-mounted source, database and editor data; dependency install occurs at PHP startup. | Preserve actual operational mechanism once confirmed; build immutable production images with narrow writable mounts and no runtime dependency downloads. | Clean release build, stack deployment rehearsal, health checks and restore/rollback rehearsal. |

Twig's locked version falls within published advisory ranges. A direct Packagist advisory lookup was performed; this is not a completed `composer audit` of the whole application. Exploitability depends on features used: ReLiS renders repository templates with no configured Twig sandbox in the inspected code. Update to a reviewed stable Twig 3 release (3.30.0 is the current candidate checked), rather than merely the first release fixing an older advisory. [Twig package versions](https://packagist.org/packages/twig/twig), [maintainer advisories](https://github.com/twigphp/Twig/security/advisories)

## 3. Framework and PHP compatibility work

Preserve these verified changes against upstream CodeIgniter 3.1.13:

- `relis_sys/helpers/string_helper.php`: added `endsWith()` helper, plus formatting/comment changes.
- `relis_sys/helpers/url_helper.php`: added `tomcat_api_url()` and `biblerproxy_url()`, plus formatting changes.
- `relis_sys/libraries/Unit_test.php`: custom assertion arguments, report rendering/timing and `last_result()`.
- `relis_sys/language/english/unit_test_lang.php`: labels for the custom test report.

Upstream reference: [CodeIgniter 3.1.13 source archive](https://codeload.github.com/bcit-ci/CodeIgniter/zip/refs/tags/3.1.13). The version constant alone does not prove PHP 8.4 support. Keep a concise patch ledger with upstream commit/advisory references, local rationale and tests; use compatible upstream fixes where available, with reviewed minimal backports otherwise.

Specific findings to resolve, then verify at runtime:

1. **Dynamic properties:** CI controllers and Loader attach properties dynamically; application libraries also assign undeclared `$this->CI`, database handles and loaded services. Evaluate narrowly scoped `AllowDynamicProperties` on the CI classes designed for injection; use declared properties in ordinary application classes. Inspect inherited behavior and CI's `__get` paths before changing models. [PHP 8.2 migration guide](https://www.php.net/manual/en/migration82.deprecated.php)
2. **Session compatibility:** `Session.php` reads/modifies `session.sid_length`, configures cookie-only/trans-SID settings and derives its validation regexp from old ID lengths. PHP 8.4 deprecates several of these changes. Preserve secure ID generation and regeneration while supporting the new default IDs and existing cookies; check handler interface signatures and the wrapper actually used. Plan a controlled session reset at cutover if formats cannot safely coexist.
3. **CSV behavior:** `Home.php`, `Paper.php`, `Reporting.php`, `Screening.php` and test helpers omit the escape argument to CSV functions. Specify the existing escape behavior explicitly first; do not silently change exported CSV escaping. Test quotes, backslashes, embedded line breaks, empty fields, separators and accents.
4. **Implicit nullability / deprecated configuration:** framework examples include `Encryption::encrypt/decrypt($data, array $params = NULL)`. Patch relevant nullable signatures; remove use of deprecated `E_STRICT` in entrypoints/configuration. Inspect unused libraries before calling their code compatible. [PHP 8.4 deprecations](https://www.php.net/manual/en/migration84.deprecated.php)
5. **Encoding:** active `utf8_encode()` calls exist in `Biblerproxy_lib.php` and a test helper. Determine the actual input encoding, then use an explicit conversion only where necessary. Blindly replacing this with a Latin-1 conversion could corrupt already-UTF-8 BibTeX.
6. **MySQLi behavior:** CI's `reconnect()` calls deprecated `mysqli::ping()`; many application queries call procedures and manually advance results with `mysqli_next_result()`. Exercise repeated calls, error paths and transactions to detect commands-out-of-sync or exception-handling differences.
7. **All PHP entrypoints and generated code:** lint application, framework, installer/model entrypoint templates, editor-generated project PHP and fixtures. The discovered PHP 7.3 runtime means reviewing changes through 7.4, 8.0, 8.1, 8.2, 8.3 and 8.4. Intermediate versions are optional isolated diagnostic checkpoints. Preserve routes, serialized values, dates and numerical/string comparison behavior.

Do not hide migration warnings through broader masks. Log `E_ALL` in the isolated candidate, while keeping diagnostics out of production responses. Classify pre-existing failures separately from new ones.

## 4. Deployment defects to resolve

- **Environment naming is inconsistent:** `index.php` accepts `dev`, `testing`, `prod`; database configs test `ENVIRONMENT !== 'production'`. Consequently `prod` still enables database debug. The committed `.env` sets `ENV=dev` for both Compose files, while the production Docker build argument says `prod`; a build argument does not change that runtime environment. Nginx's `fastcgi_param CI_ENV production` cannot configure PHP behind `proxy_pass`. Choose one convention and update all consumers together; verify production error responses contain no diagnostics.
- **Nonreproducible startup:** the PHP entrypoint runs Composer on the bind-mounted checkout and recursively grants mode 777 to configuration, sessions and exports. Build dependencies into the image with a pinned Composer tool; fail clearly on startup errors; run Apache with correct signal handling. Replace broad permissions with ownership and the minimum writable directories. Project provisioning still needs a deliberately writable database-config/generated-config location.
- **Apache configuration needs effective-state verification:** the custom vhost contains `__DOCUMENT_ROOT__` / `__DIRECTORY_INDEX__`, while Dockerfile substitutions target different text and there is no explicit `a2ensite` for that vhost. The default site is also edited. Inspect enabled sites with `apache2ctl -S` before deciding which configuration actually serves requests.
- **INI selection and shell scripts:** confirm effective CLI/HTTP INI files; remove obsolete or malformed settings. Dockerfile `sed 's/\r$//g'` lacks an in-place option, so it does not repair copied script line endings. Preserve LF in Git and verify both entrypoints inside Linux images.
- **Swarm build/release path:** the production file contains `build:` but no `image:` for PHP/Tomcat. Swarm does not build those images; create/publish versioned images first and reference their digests. Static config parsing passed, but this does not prove deployability. [Docker stack deployment documentation](https://docs.docker.com/engine/swarm/stack-deploy/)
- **Proxy/TLS:** publishing 443 does not configure TLS. The committed Nginx server only listens on 80. Establish whether a host/institutional proxy terminates TLS; verify canonical host, forwarded scheme/trusted proxy handling and externally reachable service ports.
- **Replica consistency:** all PHP replicas must see consistent generated project config and locked shared session storage. Avoid concurrent Composer installs/config appends in a shared checkout. Begin cutover with controlled concurrency; prove session locking and concurrent project creation before restoring intended replica counts.
- **Persistence and secrets:** distinguish read-only release files from `data/db`, editor workspace, generated PHP configuration, media, session files, exports and logs. Preserve ignored generated files in backups; Git alone cannot restore a working installation. Restrict verbose SQL general logging and retain useful rotated error/slow-query logs without collecting credentials unnecessarily.

## 5. Security work before reopening

These are source/configuration findings, not evidence that this installation has been exploited. If incident review indicates compromise, rebuild from trusted images/source and review restored executable artifacts before reuse; upgrading binaries does not remove a web shell. Preserve relevant logs and arrange credential rotation through the owner without changing hosted credentials during this planning task.

| Priority | Finding | Planned resolution / acceptance evidence |
| --- | --- | --- |
| Release blocker | Unpatched PHP 8.1.31 baseline; requested 8.4.7 also predates fixes. | Verified patched 8.4 image, effective startup settings and all workers replaced; no fallback to the vulnerable public runtime. |
| Release blocker | `unit_test` is in the authentication-free controller list; tests create/modify/delete users/projects and manipulate session files. Installer/debug/mail/server-control actions also exist. | Remove test/installation/diagnostic routes from production exposure through application and web-server controls; keep the suite enabled only in isolated testing. Verify unauthenticated requests cannot execute them. |
| Release blocker | Entire repository is document root. Root rewrite rules serve existing files directly; `relis_app`/`relis_sys` have deny files, but equivalent protection is absent for sessions, deployment SQL/configuration and other root artifacts. | Explicit deny rules/private storage for `.git`, `.env`, deployment, database dumps, vendor internals, session files, logs, generated PHP and private exports. Disable directory indexing; verify real HTTP requests. A wholesale public-directory refactor is optional. |
| Release blocker | Blank root DB access is enabled in Compose; application uses root provisioning credentials. | Password-protected private DB; scoped application/provisioning accounts and controlled routine definers. ReLiS legitimately needs DDL during project creation: prove required operations still work under the proposed grants. |
| Release blocker | Hardcoded SMTP credential-like values and CAPTCHA configuration in source; configuration is tracked. | Identify active values without reproducing them in reports; externalize secrets, rotate exposed active credentials, remove public test-mail actions. Preserve existing encryption keys if production uses them; the inspected local encryption-key setting is empty. |
| Release blocker | Tomcat 8.5 is unsupported; prebuilt editor contains old dependencies. FastAPI builds paths directly from query parameters with no authorization/canonical path checks in the inspected code; Nginx proxies its API publicly. | Retain the current Java/WAR packaging per the owner's direction and record its dependency exposure. Restrict file API to the PHP service where possible; canonicalize/allowlist generated-file paths and add authentication if externally reachable. Validate editor access. Do not silently remove essential editor functionality to claim completion. [Tomcat 8.5 end of life](https://tomcat.apache.org/tomcat-85-eol.html) |
| High | Browser CSRF protection is disabled; cookie secure/HttpOnly settings are false in application config. | Add tested CSRF handling to browser forms/AJAX with deliberate API exemptions; check effective session-cookie flags separately. Set HTTPS/Secure/HttpOnly/SameSite consistently at the actual TLS boundary. |
| High | SQL is assembled with input concatenation, including username in `User_dataAccess.php`; stored procedures alone do not prevent injection at the call site. | Parameterize input values, allowlist/quote generated database/table identifiers, and test privilege/tenant boundaries. Keep these fixes separate from compatibility patches. |
| High | Passwords use MD5; central schema/routine parameters include 40-character password fields. | Design an additive migration to at least 255-character hash storage, update all account-create/change/reset paths and routine signatures, verify legacy MD5 on successful login then rehash using `password_hash`/`password_verify`. Preserve existing users. New hashes require a rollback build that supports both formats; schedule and approve this schema/auth change separately from the runtime-only checkpoint. |
| High | Twig has known advisories; frontend, opaque Bibler image and WAR dependencies are outside a complete Composer audit. | Review reachability and replace affected components with tested supported artifacts. Record any remaining risk explicitly before reopening. Do not infer Log4Shell solely from the WAR's Log4j 1.x filename. |
| High | Uploaded/editor-produced PHP is copied and included; filename suffix checks do not establish trusted content. Broad writable permissions amplify the risk. | Treat project generation/import as privileged executable-code provisioning. Authorize operations, validate paths/project identifiers and trusted origins, prevent direct web execution/download of generated configs, test uploads and cross-project access. |

Security changes that affect clients or data need their own reviewable checkpoints. Compatibility readiness and permission to reopen are separate milestones.

## 6. Baseline evidence and current limits

Completed during this assessment:

- Verified clean starting checkout, `main` and local `origin/main` at the same commit, and the live remote `refs/heads/main` at that exact SHA using `git ls-remote`.
- Read manifests, deployment files, application config/entrypoints, CI workflow, test orchestration/helpers, database seed and representative business/integration paths; inspected the WAR without executing it.
- Compared the complete framework directory with its matching upstream archive; inventoried lockfile versus installed dependencies and performed a direct advisory lookup.
- `docker compose -f deployment/docker-compose.yml config --quiet`: exit 0.
- `docker compose -f deployment/docker-compose-prod.yml config --quiet`: exit 0.
- `docker stack config --compose-file deployment/docker-compose-prod.yml`: exit 0.

At the initial assessment, the runtime baseline could not be established: Docker client 24.0.5 / Compose 2.20.2 could not reach the stopped daemon, and host PHP/Composer were absent from PATH. Those initial findings did not include executed PHP/HTTP/database checks. Docker was running when implementation began; the actual baseline and executed verification are now recorded in section 11. The host Docker installation's maintenance remains a separate operational concern.

The repository's existing check calls:

```text
GET /test/unit_test/relis_unit_test/last_result
GET /test/unit_test/relis_unit_test                 # HTML report
GET /test/unit_test/relis_unit_test/raw_data        # diagnostic result data
```

Use the last-result endpoint once per run; generating another report invokes the suite again and can change state. Capture details in the same execution when improving the harness. The HTTP helpers hardcode `host.docker.internal:8083`, use a shared cookie file and read/write session files. Tests depend on central/demo databases, admin/demo users, generated project files, editor fixtures and Bibler. They are database/HTTP integration tests despite their unit-test name.

The custom `last_result()` returns `successful` even when its result array is empty. CI currently trusts this single word, sleeps a fixed ten seconds, runs chmod 777, and stops every container on the runner. Add assertion-count checks and a negative harness test; use bounded readiness checks and project-scoped cleanup. Preserve test assertions and ordering until independence is demonstrated.

## 7. Implementation phases and review boundaries

Each phase should become a small reviewed change set on this branch once implementation is approved. Commits/pushes require authorization. Store transient reports, sanitized dumps and fixture workspaces in an explicitly ignored private location; keep only reusable tooling and concise operating instructions in Git.

### Phase 1 — Isolated baseline and recoverable fixtures

Depends on a working Docker engine. Prepare a minimal **self-contained migration Compose configuration**, proposed as `deployment/docker-compose.migration.yml`, with configurable source snapshot, exact PHP/database image, unique Compose project, private data/workspace/session/export mounts and loopback-only HTTP ports. Do not merely overlay the existing bind mounts: `../data/db` and fixed container names would still risk sharing live/local data. Create separate baseline/candidate source snapshots and isolated vendor trees; startup currently writes into source config.

Retain the captured PHP 7.3.30 image only as an offline reference; PHP 8.1.31 is the source declaration, not the discovered baseline. Record exact image IDs and source DB version; also pin editor/Bibler artifacts. Do not launch the vulnerable reference on a public interface. Capture SMTP locally, stub/sandbox CAPTCHA and restrict test egress. Supply central/project databases, generated configs, editor workspace and seed users from sanitized fixtures. Make test URL/cookie paths configurable and apply identical harness instrumentation to both snapshots. Use private Linux source storage for meaningful case-sensitivity and performance checks on Windows hosts.

Verify container mounts and database identity before enabling destructive tests. Run the existing suite; record image digests, configuration, exit codes, assertion totals, warnings and reproducible failures. Capture representative HTTP/export behavior and workload timings. Completion: repeatable isolated run and successful restore of the fixture set, or an explicit baseline blocker with its consequences. Rollback: stop only the migration Compose project; original data and runtime remain untouched.

### Phase 2 — Reproducible PHP 8.4 container and dependencies

Depends on phase 1. Change `deployment/Dockerfile`, INI files, entrypoint, Compose/runtime configuration and Composer manifest/lock. Pin patched PHP/OS/Composer, correct required extensions, use compatible development Xdebug, preserve LF scripts and build production dependencies at image build time. Keep CLI/Apache configuration verifiable and restart behavior predictable. Fix environment naming/error display consistently across `index.php`, installer/model entrypoints and config examples.

Inspect Composer scripts/plugins/platform overrides and installed-package differences first. Update Twig and necessary related packages as a limited group; preserve the lockfile. Do not use an unrestricted first update or ignore platform requirements. Keep baseline and target vendor directories separate. Completion: candidate image builds from a clean snapshot, CLI and Apache report the intended runtime/extensions/settings, platform checks pass and production artifact installs reproducibly. Rollback: select the previous local image/source snapshot; never put PHP 8.1 back on public traffic.

### Phase 3 — CodeIgniter 3 and application compatibility

Depends on phase 2. Patch the specific CI/application issues in section 3, preserve the four framework modifications and validate session handlers/injection behavior. Update CSV/encoding/signature code with regression fixtures; do not change business rules. Use a PHP 8.4-capable compatibility scanner in isolated development tooling if it adds useful coverage, then review findings manually.

Run Linux lint, targeted regression tests and the full original suite with `E_ALL` logged. Include project configuration generated by the editor and legacy installs. Completion: no unexplained migration regressions, all required suites execute nonzero expected assertions, and warnings are fixed or individually accounted for. Rollback: revert only the compatibility change set and use the prior isolated image/data snapshot.

### Phase 4 — MariaDB 11.4 restore and upgrade rehearsal

Depends on phase 1; final application validation also depends on phase 3. Establish production's exact source version and all project database names first. If source is already 11.4, test a maintenance update; if older, follow the documented supported path for that version or test a logical migration into a fresh 11.4 instance. Never attach a newer MariaDB binary to the only copy of an old data directory.

Prefer a fresh target instance and logical restore for a manageable offline dataset; measure dump/restore duration and capacity before finalizing. Back up all application databases with routines, triggers, events and binary data, plus user/grant definitions separately. Do not blindly restore an old `mysql` system schema into a fresh newer server. Because application DDL can run during provisioning, freeze writes/DDL for the final consistent backup; `--single-transaction` alone cannot make concurrent schema changes safe.

Test root-definer replacement and provisioning grants; compare schema, object counts, row counts, representative checksums and stored SQL modes. Preserve existing latin1/utf8/utf8mb4 semantics and date handling; charset normalization is separate. Exercise routine multi-results, GROUP BY/report queries, database creation/deletion and Unicode imports. Run `mariadb-upgrade` where the selected physical/server upgrade procedure requires it; it is not a substitute for testing a logical restore. Completion: central plus all selected project data restores, application/routine tests pass and rollback restore is rehearsed. Rollback: restore the pre-upgrade backup into a separate instance running its matching server version; never downgrade the upgraded directory in place. [Upgrade guidance](https://mariadb.com/docs/server/server-management/install-and-upgrade-mariadb/upgrading/platform-specific-upgrade-guides/upgrading-on-linux/upgrading-between-major-mariadb-versions), [downgrade limitations](https://mariadb.com/docs/server/server-management/install-and-upgrade-mariadb/downgrading-between-major-versions-of-mariadb)

### Phase 5 — Security and supporting services

Depends on the corresponding baseline fixtures; can be developed independently of parts of phases 3/4, but validate on the final combination. Address section 5 through separate PHP/access-control, Java/Python API, dependency and authentication changes. Preserve the existing WAR/Bibler packaging as requested by the owner. Make necessary integration and boundary changes here, verify editor/BibTeX behavior and record the retained dependency risk for the reopening decision.

Separate additive password/schema work from runtime fixes; prepare a previous-release-compatible rollback build before introducing modern hashes. Frontend updates should target loaded vulnerable components and be checked against actual screens. Completion: release blockers resolved, negative authorization/path/injection tests pass, dependencies audited and any remaining risk accepted explicitly. Rollback: only to an artifact retaining the required security fixes; otherwise remain in maintenance mode.

### Phase 6 — CI and complete release validation

Depends on phases 2–5. Update `.github/workflows/relisUnitTest.yml` to use the isolated test environment, pinned tools/actions, bounded health/readiness checks, reliable nonzero test counts and scoped cleanup in an always-run step. Remove permission workarounds. A baseline job is temporary/private and must not become a public vulnerable service; the required final job is patched PHP 8.4 plus the chosen MariaDB 11.4 patch.

Run the workflow matrix in section 8 on Linux through Apache and the real proxy/editor/Bibler stack. Build production with `--no-dev` and no Xdebug, test multiple PHP replicas and compare timing/memory/query performance with baseline. Completion: a clean checkout can reproduce the exact artifact and required passing checks with retained reports. Rollback: use the last verified CI/image change set; do not weaken assertions or suppress new diagnostics.

### Phase 7 — Staging, deployment and rollback rehearsal

Depends on phase 6 and confirmation of actual hosting. Build/publish the PHP release image, pin the retained Tomcat/Bibler artifacts and validate the real Swarm or Compose deployment path. Confirm TLS, secrets, private storage, session locking, health checks, log rotation, resource limits and backups. Rehearse from sanitized production-like data, including rollback with generated config/workspace/uploads restored together.

Freeze writes and provisioning, pause discovered jobs, back up all mutable state, restore/upgrade a separate target DB, switch reviewed code/config/image references and replace all PHP workers. Keep maintenance mode until the smoke tests pass. Resume jobs and traffic only after the readiness decision; record versions/digests and the cutover time. Completion: staging passes the same acceptance checks and measured restore/rollback time meets the agreed outage window. Production execution requires separate authorization.

## 8. Commands and acceptance matrix

The isolated Compose configuration is implemented. Follow [migration/README.md](migration/README.md) for the complete preparation, dependency installation, startup and cleanup sequence. It requires the generated private environment file; do not overlay it onto the ordinary stack or run it against `data/db`. The already prepared PHP-only candidate uses these verification commands:

```powershell
$fixture = 'data/migration/php84/.env'
$compose = 'deployment/docker-compose.migration.yml'
docker compose --env-file $fixture -f $compose config --quiet
docker compose --env-file $fixture -f $compose ps
docker compose --env-file $fixture -f $compose exec -T relis-application php --ini
docker compose --env-file $fixture -f $compose exec -T relis-application composer validate --strict
# Run the online Composer audit in the separate read-only container described in migration/README.md.
docker compose --env-file $fixture -f $compose exec -T relis-application composer check-platform-reqs --no-dev
docker compose --env-file $fixture -f $compose exec -T relis-application php deployment/migration/lint.php
docker compose --env-file $fixture -f $compose exec -T relis-application php deployment/migration/check-runtime.php
docker compose --env-file $fixture -f $compose exec -T relis-application php deployment/migration/run.php test
# Preserve the evidence and volumes when stopping only this fixture:
docker compose --env-file $fixture -f $compose down
```

Use `composer outdated --direct` and a reviewed dry-run/targeted `composer update twig/twig --with-dependencies` in the isolated candidate dependency workspace when selecting the lock update. Substitute the actual chosen PHP patch for 8.4.27 in compatibility inspection. Record expected advisory failures on the old lock rather than bypassing security policy.

For syntax checking inside the Linux candidate, use a small runner that invokes `php -l` for each tracked/generated PHP file and fails if **any** invocation fails. Include `relis_app`, `relis_sys`, `install`, root/model entrypoints and generated project files. A raw `find -exec php -l` invocation by itself is insufficient to guarantee a failing overall exit status. Keep any runner small and reusable.

A private HTTP diagnostic should return only PHP version/SAPI, relevant loaded extensions, INI paths and selected nonsecret settings; remove it from the release artifact. Do not expose a public `phpinfo()` page. Inspect server settings through a scoped DB account, for example:

```sql
SELECT VERSION(), @@hostname, @@port, @@sql_mode,
       @@character_set_server, @@collation_server, @@time_zone;
SELECT DATABASE(), CURRENT_USER();
SELECT SCHEMA_NAME, DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
FROM information_schema.SCHEMATA;
```

| Workflow / boundary | Required evidence |
| --- | --- |
| Existing automated suite | Expected nonzero assertions and suite totals, failures/skips/warnings recorded; intentional failing assertion is reported as failure; no new unexplained regression. |
| Authentication | Existing-user login, logout, invalid login, registration/confirmation, reset/change, expiration and session regeneration; SMTP captured, no real mail. |
| Authorization | Admin/manager/reviewer/anonymous behavior, private/public reviews, cross-project denial, API/APIQuery permissions, production denial of tests/install/debug actions. |
| Project lifecycle | Create using DSL editor and trusted uploaded config, install/reinstall, change title/short name/schema, assign users, switch between projects, delete only disposable project; generated PHP and all database objects remain consistent. |
| Papers and review work | BibTeX/CSV imports including duplicates/bad input, screening modes/conflicts, inclusion/exclusion, assignments, quality assessment/validation and data extraction edit round trips. |
| Reports / downloads | Compare CSV/BibTeX/R/Python/ZIP output against baseline fixtures, with deliberate treatment of timestamps/order; exercise accents, quotes, backslashes and large files. |
| UI | Representative forms/AJAX, paginated/filterable tables, widgets and charts, no new browser console failures or unintended CDN/version conflicts. |
| Upload/private files | MIME/size/path rejection, image behavior, downloads with authorization, direct requests to secrets/session/dump/generated-PHP paths denied. |
| Database | Central and multiple project restores, routine/definer/grant validation, transactions/rollback, repeated procedure calls, concurrent writes and report query behavior. |
| Services / failure modes | Editor and file API contracts, Bibler success/error/timeout, SMTP capture, CAPTCHA outcomes, bad proxy paths and controlled service outages. |
| Runtime / performance | CLI versus Apache versions/settings, health checks, production diagnostics hidden/logged, session locking across replicas, memory/latency/query comparison for representative review sizes. |
| Operations | Clean build, reproducible `--no-dev` image, all mutable artifacts backed up, source-version-aware DB restore and complete staging rollback rehearsal. |

No queue/cron/worker test command can be specified until actual host-managed jobs are identified. CLI execution is not established just because CodeIgniter supports it; inspect and test any operational commands found during hosting discovery.

## 9. Cutover rollback rules

Keep old and new DB/storage snapshots separate. Reverting a Git branch or Docker tag cannot undo an incompatible database upgrade, modern password hashes, new project definitions or other writes.

Before reopening traffic, a failed smoke test can roll back to the exact pre-cutover data/config snapshot with no accepted user writes to lose. After traffic resumes, rollback must preserve legitimate writes: prefer a forward fix or a rehearsed compatible application rollback against the current database; otherwise freeze writes and reconcile database/binlog changes and mutable files through an explicitly tested recovery procedure. Do not promise a lossless downgrade without that procedure. Set the write-resumption point and recovery objectives before production deployment.

The vulnerable reference image is an offline diagnostic tool, not a safe public rollback target. Prepare a previous-behavior build running patched PHP if online rollback is required; otherwise rollback means remaining offline while restoring a secure service.

Trigger maintenance/rollback for failed login or authorization, corrupt imports/exports, project provisioning failures, missing data/configurations, persistent session failures, serious error-rate increases or material workload regressions. Set numeric performance/error thresholds from the baseline and agree the recovery time/data-loss objectives before staging sign-off.

## 10. Information still needed and readiness decision

1. Actual last production PHP/MariaDB versions and image digests, whether it uses this Swarm file, host Docker/OS versions, TLS termination and public/internal routes. The production question remains open at the time this document was written.
2. A sanitized representative snapshot of **all relevant central/project databases**, generated config files, editor workspace and media; data volume and acceptable outage window. Obtain through the established authorized backup process.
3. Confirm deployment exposure and access controls for the retained WAR/Bibler services. The owner requested keeping their present form; external source retrieval is not an implementation dependency.
4. Active SMTP/CAPTCHA configuration, secret storage, operational jobs, backup retention, writable paths and known incident exposure. Do not paste secrets into this plan.
5. Representative production workloads and staging access. The local baseline is now measured: 433 assertions, 33 existing failures; details follow.

The assessment and initial runtime checkpoint are complete. The measured runtime results are recorded below; **complete functional acceptance, staging readiness and production readiness remain unverified.** The release can be considered ready only after the agreed phases execute, required workflows pass, relevant security findings are resolved, and a source-version-aware restore/rollback rehearsal succeeds. Update this document with concise measured results as the work proceeds.

## 11. Implementation evidence — phase 1

Implemented on `codex/modernize-php84-mariadb11`; `main` remains at `54084cc`. The ordinary development containers and their data were not stopped or migrated. Reference services are a separate Compose project with loopback-only HTTP, internal networking, fresh DB/workspace/source volumes, local SMTP capture and a unique DB identity checked before destructive tests.

- `deployment/migration/prepare.py` snapshots the assessed Git ref, overlays only the listed test instrumentation and private fixture configuration, records exact cached image IDs, and seeds a Linux source volume. It refuses the wrong Git branch and an existing snapshot/source volume. The captured PHP image cannot be rebuilt exactly from today's different Dockerfile; retain it as the offline reference.
- Harness changes parameterize host/cookies, remove remaining fixed-port API/helper accesses, add a machine-readable report without rerunning the suite, and make zero assertions fail. Original assertions and suite ordering remain intact. A dotted `relis.test` alias fixes a reproduced cookie reload failure in the old libcurl. Migration-only INI disables old debugger autostart; no application warning suppression was added.
- Fixture bootstrap installs the demo project's actual database/configuration through application routes. The seed alone only advertises that project. Readiness requires its `paper` table and generated configuration; baseline tests use repository fixtures rather than copying existing user data.
- Seven focused harness checks pass. Empty/mismatched fixture identities were rejected with exit 2 before test actions. PHP syntax checks passed for all seven changed/new PHP harness files. Compose validation and bounded health checks passed for all five services. Resolved/running mounts, volume labels and internal network isolation were inspected.
- A fresh same-version restore passed for three fixture schemas: **47 tables, 250 routines**, identical row counts/checksums and routine definers/SQL modes. Private SQL/config backups and verification JSON remain ignored under `data/migration/baseline-linux`. This verifies baseline recovery, not the MariaDB maintenance upgrade or production recovery.
- Full suite command: `docker compose --env-file data/migration/baseline-linux/.env -f deployment/docker-compose.migration.yml exec -T relis-application php deployment/migration/run.php test`. Result: **exit 1, 433 assertions, 400 passed, 33 failed, 327.16 seconds**, PHP **7.3.30 / apache2handler**, MariaDB **11.4.2**. HTML/JSON/diagnostic reports were captured from that single run; outer diagnostic output was empty. This is a failing reference baseline, not a migration success.

Initial failure classification, retained for comparison before PHP changes:

| Group | Count | Evidence / remaining work |
| --- | --- | --- |
| Redirect expectations | 18 | User/project/paper/manage tests expect HTTP 307 while the unchanged HTTP helper follows redirects and reports final HTTP 200. Establish the intended response measurement before changing harness semantics. |
| Screening/session context | 6 | Four screening and two perspective checks; observed SQL includes a missing screening phase, or attempts to read project `screen_phase` from central `relis_db`. Separate fixture/session assumptions from application defects. |
| Classification redirects | 2 | Removal tests reach a 404 after redirect; the controller accepts a redirect string as one URI argument while callers include a controller/method path. Investigate the existing route construction. |
| Export comparisons | 7 | Five CSV/BibTeX comparisons and two R/Python ZIP content comparisons. The legacy failure label says "File not generated" even when its branch actually means a content mismatch. Preserve and compare generated bytes against fixtures before deciding changes. |

The earlier Windows source-bind exploratory run was superseded: it eventually exceeded the existing 900-second request limit. Its migration containers were removed with project-scoped `down`; its private evidence is separate from the completed Linux baseline. The official PHP **8.4.27** image was downloaded and its CLI version verified; no ordinary development/production service was switched to it during phase 1.

## 12. Runtime implementation and framework patch ledger

The branch now builds PHP **8.4.27 Apache / Debian Bookworm**, pinned to the official image manifest `sha256:5d55c49594dd833e2e4ff677b65f7d8ff48817f443a60691d9b2b382413bba42`. Composer **2.11.2** is checksum-verified during build. Production excludes Xdebug; the separately built development variant has **Xdebug 3.5.3**, trigger activation and discovery disabled. Both variants build and start their CLI without startup diagnostics. The effective shared INI enables OPcache with timestamp validation for generated project PHP, logs `E_ALL`, hides startup errors and disables upload-progress parsing before requests. Canonical environment names are `development`, `testing`, `production`; `dev`/`prod` remain accepted aliases. The production Compose environment explicitly overrides the development dotenv value.

The enabled Apache vhost is now the one configured by the Dockerfile; substitutions match its placeholders, logs go to container stdout/stderr, and indexing is disabled. A private production-mode Apache probe with an intentionally unreachable database returned HTTP 500 with diagnostics hidden and the failure logged; its source mount was read-only and its network disabled. The entrypoint fails on missing installed dependencies, uses narrow ownership changes and executes Apache as its final process. It no longer downloads dependencies or grants mode 777 at startup. This is a **runtime image checkpoint**: the complete immutable application release artifact and Swarm deployment path remain phase 6/7 work. Do not use the modified ordinary Compose files against the existing data directory before a reviewed backup/restore procedure.

The Composer manifest now requires PHP **~8.4.27** (8.4.27 or a later 8.4 patch) and actual required extensions. A targeted isolated update selected **Twig 3.30.0**, Symfony deprecation-contracts 3.8.1 and current compatible ctype/mbstring polyfills. `composer validate --strict`, `check-platform-reqs --no-dev` and `audit --locked` passed; the audit returned no advisories or abandoned packages. Dependencies were installed only in candidate volumes; the host's ignored vendor directory was not upgraded beneath the PHP 7.3 development container.

CodeIgniter remains **3.1.13** with local compatibility patches. The four original application-specific framework extensions listed in section 3 are preserved. These patches are local changes against the recorded upstream tag, not an upstream claim of PHP 8.4 certification:

| Files / boundary | Change and rationale | Verification |
| --- | --- | --- |
| `core/Controller.php`, `core/Loader.php`, `core/Model.php`, `libraries/Profiler.php` | Permit intentional dynamic injection. Models must retain `__get` delegation to controller services until they assign their own handle; declaring those model properties would hide that delegation. | Full assertion comparison and a focused screening-model test covering initial lookup, project switch and local override. |
| `core/URI.php`, `core/Router.php`, `database/DB_driver.php`, `libraries/Table.php` | Declare existing configuration/URI/failover/template fields. Ordinary application libraries also declare their existing `$CI` member. | Syntax and integration run, with deprecations enabled. |
| `libraries/Session/Session.php` | PHP 8.4 uses native 32-character hex IDs without modifying deprecated length/encoding settings. Verify cookie-only/trans-SID startup settings; retain old-runtime behavior below 8.4. The existing PHP 8 session wrapper already provides the required handler compatibility and was retained. | Real Apache requests test creation, persistence, HttpOnly/SameSite flags and rejection of unknown, legacy-length and malformed IDs. Cutover should expect existing users to log in again. |
| `database/drivers/mysqli/mysqli_driver.php` | Replace deprecated `ping()` on PHP 8.4 with a freed `SELECT 1` liveness query. Preserve invalidation of lost connections and subsequent initialization. | Live/killed/reinitialized connections under both silent and strict mysqli reporting modes; only test-owned connections are killed. [PHP mysqli documentation](https://www.php.net/manual/en/mysqli.ping.php) |
| `libraries/Encryption.php`, `core/Exceptions.php`, `libraries/Xmlrpcs.php`, `libraries/Pagination.php` | Explicit nullable array parameters, legacy numeric error-level mapping, current string interpolation and string input for digit validation. | Compile diagnostics and application routes. [PHP 8.4 deprecations](https://www.php.net/manual/en/migration84.deprecated.php) |
| Application CSV/import/export paths and test helpers | Specify the prior backslash escape argument, replace deprecated `utf8_encode` with its explicit Latin-1 equivalent and preserve former empty defaults for optional values. Remove a deprecated line-ending setting from a BibTeX function that parses strings. | Differential PHP 7.3/8.4 check: all 256 Latin-1 input bytes and CSV quote/backslash/newline/empty/Unicode cases produce matching bytes and parsed fields. This preserves existing conversion behavior; it does not claim that all legacy input boundaries are correctly UTF-8. |
| Application form/list configuration and profiler | Handle absent optional display fields explicitly. Repair the unused custom profiler's invalid dynamic-property syntax and legacy constructor. Enable CI error logging by default. | Whole-source lint and HTTP integration; invalid/stale business inputs remain separately tracked. |
| Test runner/helpers | Declare test-owned result fields, preserve assertions and order, compare captured assertion identities, and synchronize candidate sources without altering private configuration or the reference. | Harness checks, fixture guards and full baseline comparison. Comparison remains nonzero for any inherited failure or captured diagnostic. |

Twenty focused PHP 8.4 runtime checks pass, including missing/numeric/malformed pagination segments. The PHP-only candidate's corrected integration run on MariaDB 11.4.2 completed **433 assertions: 400 passed, the same 33 failed, no newly failing or resolved assertion**, in 315.06 seconds. Three remaining outer diagnostics came from test-only result properties and were fixed before preparing the combined candidate. The first exploratory candidate result and its corrective comparison are retained separately under ignored `data/migration/php84/reports/attempt-1`; they do not replace the frozen reference.

## 13. MariaDB maintenance checkpoint

Both ordinary Compose definitions now pin **MariaDB 11.4.13** to manifest `sha256:d1891ede5a559f1086f55053bb8cc1569b0298338c4d72d71725a7f74f6ba620`. The downloaded image reports `11.4.13-MariaDB-ubu2404`. No existing development or production data directory has been attached to that binary.

`migration/check-restore.py` restored the **same guarded bootstrap dump** into fresh isolated 11.4.2 and 11.4.13 servers. It passed for **47 base tables, 16 views and 250 routines**: row counts, base-table checksums, columns/collations, schema defaults, indexes, view definitions, procedure parameters/bodies/definers/SQL modes, triggers and events matched. Servers had no network, published ports or host data mount; their temporary storage and containers were removed after label-verified cleanup. Evidence: ignored `data/migration/baseline-linux/reports/restore-11.4.13.json` and its private details file. This verifies the repository fixture's logical maintenance migration, not a production dataset or physical in-place upgrade.

The separate `php84-db114` candidate runs PHP 8.4.27 and MariaDB 11.4.13 with the retained editor/Bibler artifacts. Its own demo bootstrap and all twenty runtime checks passed. The first complete combined run matched the reference: **433 assertions, 400 passed, 33 failed**, no newly failing assertion and no outer diagnostic output, in 324.28 seconds. The final application run after those warning fixes completed in **317.29 seconds**, again **400 passed / 33 failed**, with exactly the same ordered assertion identities and failures, no outer diagnostic output, and **no logged PHP deprecations**. Per-request capture retained **seven warnings across five locations**: two stale exclusion lookups, four missing screening-config fields and one missing export download. These need explicit input/fixture handling in the following checkpoint; they were not hidden or waived. The raw suite and comparison both exit 1.

Final source lint covers **487 files**, including generated fixture configurations, with **zero syntax failures and zero compile diagnostics**. Seven harness checks, twenty runtime checks, and seven comparison exit-status checks pass. The comparison rejects logged/outer diagnostics, missing log evidence, changed assertions, failures and zero assertions. Final Composer checks pass with no advisories/abandoned packages; the minimum PHP constraint was tightened without changing any locked package version. Reports remain private under `data/migration/php84-db114/reports`.

The old reference and PHP-only candidate containers were stopped with their own Compose projects after evidence capture; their volumes and reports remain. Only the combined candidate is retained for local inspection, on loopback port 18086 with an internal network. The ordinary development services were not restarted or migrated. `main`, `origin/main` and the migration branch's base remain `54084cc78429efbc0c05f7786110a33309b7cb1f`.


## 14. Remaining gates after the runtime checkpoint

This section records the gates at the end of the runtime checkpoint. Section 16 records the subsequent Step 1 repairs; the original failing reports remain preserved.

This checkpoint does not authorize reopening the site. The inherited suite still fails and a failed result remains nonzero. Resolve the 33 baseline failures with evidence, beginning with redirect measurement, fixture/session context and export byte comparisons. Matching assertion outcomes cannot establish equivalence where both versions already fail the same test. Review the captured per-request diagnostics separately; legacy invalid/stale input cases still need explicit handling.

Next complete the security/access-control stage and the production release build/CI workflow before staging. Password-schema changes remain a distinct behavior/data checkpoint. Keep the retained WAR/Bibler integrations functional and make necessary boundary changes locally, in accordance with the owner’s direction. Production source-version discovery, a representative backup of all review databases/configuration/workspace/media, staging access and measured rollback remain prerequisites for a production cutover. No existing data directory has been upgraded, no production action has been taken, and no commit or push has been made.

## 15. Next implementation sequence — 2026-11-10

This is the next-step plan requested after the runtime checkpoint, not a record of further implementation. Continue exclusively on `codex/modernize-php84-mariadb11`. Keep `main`, the frozen reference fixture, existing development data and production untouched. Retain the current WAR/Bibler services; make necessary integration and boundary changes in this repository. No external source-repository search or service rewrite is planned.

### Step 0 — Preserve the current checkpoint

Review the current diff and framework patch ledger, confirm all required new migration files are included despite global ignore rules, and make a local checkpoint commit on the migration branch. Include only reviewed source/documentation; exclude private reports, credentials, database dumps, generated configuration, vendor directories and data volumes. Keep subsequent work in separate commits for test repair, application defects, access controls, authentication/schema, release packaging and CI. This planning request does not itself perform that commit.

Exit criterion: the measured PHP 8.4.27/MariaDB 11.4.13 checkpoint is recoverable from Git, with its source revision and private evidence locations recorded. A checkpoint commit does not indicate production readiness or trigger a merge/push.

### Step 1 — Resolve the 33 inherited failures and seven warnings

Work in the following order, using focused reproductions before a full run:

| Work package | Implementation plan | Required evidence |
| --- | --- | --- |
| 18 redirect assertions | Record the initial HTTP status and redirect chain separately from the final followed response in the HTTP helper. Use the appropriate value for each assertion; verify destination and absence of unauthorized changes. Preserve legitimate redirects used by other test workflows. | Expected initial 307 responses and final destinations are observed. No blanket conversion of expected 307 to 200 and no global redirect-following change. |
| Six screening/session assertions | Establish the intended user, project database, screening phase and perspective through application requests. Investigate missing phase values and accidental central-DB queries. Separate test setup defects from application defects; keep model/controller DB sharing behavior covered. | Correct assignment counts and perspective/session values; no malformed screening SQL or project queries against central tables. |
| Two classification removal assertions | Trace the existing controller/method redirect path through URI arguments. Repair route construction with an allowed internal destination; preserve the paper identifier and verify the classification update independently of the redirect. | Classification is deactivated, the complete destination resolves, and external/unintended redirects are rejected. |
| Seven export assertions | Retain generated CSV/BibTeX/R/Python/ZIP outputs before test cleanup. Compare actual bytes and archive members with the reference and repository fixtures; identify encoding, ordering, content or fixture defects individually. Normalize only proven nonsemantic metadata. | Correct outputs match reviewed expectations. No wholesale regeneration of golden files simply to make tests pass. |
| Seven logged warnings | Handle missing exclusion records in both legacy controllers, determine the required default/phase screening-config fields, and handle a missing export before calling `readfile`. Correct invalid fixture inputs where appropriate; define deliberate responses for invalid client requests. | No undefined fields/null offsets or missing-file warnings. Invalid input does not cause partial writes or disclose paths. Intentional response changes have explicit assertions. |

Preserve coverage of the original 433 assertions. Add focused regression cases where behavior is repaired; the total may therefore increase. Keep raw runner/comparison failures meaningful, and never suppress warnings or disable failing assertions. Preserve the original failing reference and the initial candidate reports.

Exit criterion: the complete candidate suite passes with nonzero expected assertion counts, zero outer/logged PHP diagnostics and no lost test coverage. Re-run the twenty runtime checks and source lint for the final batch.

### Step 2 — Close exposed routes, files and service boundaries

1. Deny test, installer, debug and test-mail actions in production at the application boundary. Ensure migration tests remain available only in the explicitly guarded disposable environment. Removing a navigation link is insufficient.
2. Deny direct HTTP access to repository metadata, dotenv/configuration files, SQL backups, deployment files, logs, sessions and generated executable project configuration. Move sensitive runtime storage out of the served tree and preserve authorized downloads and privileged project generation.
3. Review project-creation/configuration upload authorization and trust. Validate project/file identifiers, prevent traversal and cross-project access, and restrict execution-capable imports to trusted provisioning operations.
4. Keep the Java editor and Bibler intact. Restrict the Tomcat file API to required callers, validate canonical paths beneath the correct project directory, and protect browser/editor routes without breaking editor access or required proxy behavior. No public database/admin-service exposure in the release configuration.
5. Remove the unconditional CAPTCHA bypass from production behavior. Replace integration-test use with an isolated test mechanism; verify CAPTCHA TLS, bounded timeouts and fail-closed errors. Externalize SMTP/CAPTCHA/DB secrets and remove runtime use of committed credential values. Prepare credential rotation through the actual hosting configuration, without changing production accounts during local work.
6. Parameterize SQL values, beginning with login/username checks and privileged project operations. Allowlist generated database/table identifiers and validate tenant boundaries. Inventory the browser assets actually loaded and apply necessary targeted security fixes with screen checks.

Exit criterion: positive workflows still work, while anonymous, wrong-role, wrong-project, traversal, injection and direct-sensitive-file requests are denied. Validate access controls on the actual production image/proxy configuration once step 4 supplies it. Retained service dependency exposure remains documented.

### Step 3 — Modernize authentication and database privileges

Treat this as its own schema/behavior checkpoint after step 1, following the SQL/access-control groundwork in step 2:

- Add support for modern password hashes without losing existing users: widen password storage and every relevant routine parameter to at least 255 characters, verify legacy MD5 credentials only during the transition, and rehash after a successful legacy login. Use modern hashes for registration, reset and password changes. Failed authentication must not alter stored credentials.
- Prove mixed legacy/modern accounts, registration/confirmation, login/logout, reset/change, expiration and regeneration. Rehearse schema changes on copied data and prepare a patched rollback build that understands both hash formats before enabling hash writes. Reverting to the old MD5-only application is not a valid rollback.
- Add tested CSRF handling for browser forms and AJAX with narrowly defined API behavior, plus HTTPS/Secure/HttpOnly/SameSite settings at the real TLS boundary. Preserve legitimate integrations and session locking/regeneration; verify the documented login reset on cutover.
- Replace empty-root/application-root access with dedicated accounts and deliberate routine definers. Prove the grants support both ordinary review operations and authorized project schema/routine creation, modification and deletion. Keep provisioning privileges separated from routine request access where the application design permits it.

Exit criterion: authentication/security tests and the full review suite pass on the migrated schema and chosen accounts; schema/auth rollback is demonstrated on disposable data. No charset normalization is bundled into this change.

### Step 4 — Build the release artifact and make CI reproduce it

Build the application and locked production dependencies into the release image. Remove the production dependency on a host checkout/vendor directory, exclude test-only artifacts/secrets, retain only necessary writable storage, and keep Xdebug and startup dependency downloads out of production. Pin retained supporting images/tools and prepare an exact artifact manifest. Resolve the Swarm `build`/missing-image problem against the confirmed deployment mechanism rather than assuming Swarm builds images.

Replace the existing GitHub workflow with the isolated fixture workflow: pinned reviewed actions, bounded readiness checks, one captured suite execution, required assertion counts/diagnostic checks and cleanup limited to that job's Compose project in an always-run step. Remove `chmod 777`, fixed container names/sleeps and the global container-stop command. Support validated detached CI revisions explicitly without relaxing local branch protections or the DB fixture identity guard. Run without production secrets; upload only an allowlist of sanitized reports, never dotenv/cookies/configuration dumps.

Run the complete suite on a test layer derived from the release artifact and smoke-test the final production image itself, including denial of test routes and exposure checks through Nginx. Check editor access, required proxy/WebSocket behavior, trusted forwarding/TLS settings and session behavior with the configured PHP replica count. Confirm exported analysis artifacts can be consumed in a separate representative R/Python environment; generation and execution are distinct checks.

Exit criterion: a clean Linux checkout builds and validates the same image/lock without local vendor files, cached baseline images or developer volumes. The actual release image passes its production smoke/security checks, and CI reliably fails for an injected assertion failure, empty report or PHP diagnostic.

### Step 5 — Rehearse with representative production data in staging

Before this step, establish the actual production PHP/MariaDB/image versions, Compose versus Swarm use, TLS/storage/session arrangements, host-managed jobs, data size and acceptable outage/recovery objectives. Obtain a sanitized, consistent backup of every relevant central/project database with routines/triggers/events, plus generated PHP configuration, uploads/media and editor workspace/embedded metadata. Handle user/grant definitions deliberately; do not copy an old server system schema blindly.

Restore into a separate target instance using the source-version-appropriate procedure. Compare schemas, counts/checksums, encodings, views, routine parameters/definers/SQL modes and grants; run real project provisioning, role/project isolation, imports, screening, QA, extraction, exports and captured SMTP flows. Check representative UI/forms/AJAX/tables/charts, service timeouts and parallel sessions. Measure restore/cutover/rollback time and representative latency/memory against agreed thresholds. Never attach the new binary to the sole old data directory.

Exit criterion: staging passes the same functional/security checks using the intended deployment path, all mutable state is recoverable together, and a measured rollback rehearsal meets the agreed outage/data-loss objectives. The repository-fixture restore already completed does not substitute for this rehearsal.

### Step 6 — Prepare and execute the controlled reopening

Prepare a concrete cutover runbook and artifact/configuration manifest from the successful staging rehearsal. Final production execution, image publication, merge and reopening remain separate authorized actions; this plan does not perform them. At cutover, freeze writes/provisioning and discovered jobs, take the final consistent backup, migrate to the separate target, deploy the verified patched artifacts and complete smoke/security checks while maintenance mode remains active. Reopen only after the readiness decision; then resume jobs and monitor the agreed signals.

Rollback uses the rehearsed compatible artifacts and complete data/configuration/workspace snapshot. After accepting user writes, prefer a forward fix or a compatible application rollback; otherwise freeze and reconcile writes through the tested recovery procedure. Do not downgrade an upgraded data directory or expose the vulnerable reference image.

This was the sequence proposed after the runtime checkpoint. The owner subsequently selected Step 1; its implementation is recorded below. Steps 2–4 are local branch work; steps 5–6 additionally depend on hosting information, representative data and staging access. Missing production details do not block the local work.

## 16. Step 1 complete — inherited failures and warnings

Implemented exclusively on `codex/modernize-php84-mariadb11`. The frozen PHP 7.3 reference and the pre-repair PHP 8.4 checkpoint remain intact. Repairs were first verified in focused groups on the isolated `php84-db114` candidate, then exercised together in a new `step1` fixture with fresh source/database/editor volumes and a locked Composer install. It uses the same recorded PHP **8.4.27** and MariaDB **11.4.13** image IDs, retained WAR/Bibler artifacts, guarded fixture identity, internal network and loopback HTTP on port **18087**.

| Original failure group | Root cause and repair | Coverage |
| --- | --- | --- |
| 18 redirects | The helper followed redirects but the assertions read the final status as though it were the initial response. Capture real headers and each response separately. Keep redirect following and the original expected 307 values; assert the initial status and destination, plus successful completion of the chain. Transport errors throw instead of being treated as partial successes. | All original assertions retained; 36 destination/final-status assertions added. |
| Four screening checks | Closing a phase returns to `screening_select`, which clears the active phase. Reopen and select the Title phase through application routes before testing assignments. | Existing user/paper availability and assignment-count checks retained. No missing-phase SQL in the repaired run. |
| Two perspective checks | The preceding API-query group deleted the project. The perspective group inherited an invalid context and queried a project table in the central DB. Give the group its own authenticated project setup. | All three existing perspective assertions pass independently and in sequence. Model DB delegation remains covered by runtime checks. |
| Two classification removals | A controller/method redirect occupied two URI arguments, but the action accepted only one. Reconstruct the complete destination, preserve the paper ID and allow only the two destinations used by the application. Validate before changing the classification. | Original deactivation checks read the DB independently of the redirect. Six assertions cover complete destinations, final responses and rejection without mutation. |
| Seven exports | Every mismatch was solely CRLF versus LF in expected CSV/BibTeX or generated R/Python fixtures: eight files across seven assertions. Preserve actual/expected bytes before cleanup, set LF explicitly for only those eight fixtures and retain strict byte equality. Existing CRLF library/requirements files already matched and were left unchanged. | All original export assertions retained. Ten saved file/member comparisons match exactly; no wholesale fixture regeneration or whitespace-insensitive comparison. |

The seven warnings had three causes. Both legacy exclusion-edit controllers now reject missing records with 404 before reading fields; positive tests create a real exclusion whose ID differs from its paper ID. `Manage::edit_exclusion` also now passes the associated paper ID to the form, covered by direct controller probes. Default screening-config requests include the fields posted by the actual form; incomplete requests are rejected with 400 before configuration reads/writes, with an unchanged-data assertion. Download tests create a real export instead of requesting `index.html`, which CodeIgniter strips to `index`; the controller checks the canonical file path before headers/`readfile` and returns 404 for a missing export. Added checks cover exact downloaded bytes, attachment name and denied traversal. `E_ALL` and warning capture remain enabled.

A first fresh-fixture run passed **484/484 assertions in 329.09 seconds**, with zero outer/logged PHP diagnostics. Log review also identified two inherited SQL errors from anonymous logout during setup: the application tried to log a disconnection without a user, reaching a group lookup with an empty ID. The logout action now writes that audit entry only for an authenticated user, while retaining session cleanup and the existing redirect. Two added HTTP assertions cover anonymous logout. The raw runner and comparator now additionally require zero logged SQL query errors, including evidence that the count was captured.

The final complete run passed with **exit 0: 486 assertions, 486 passed, zero failed, in 325.26 seconds** on PHP 8.4.27 / MariaDB 11.4.13. Both outer and logged PHP diagnostics are **zero**, and the captured request interval contains **zero SQL query errors**. The raw runner and baseline comparison both exit 0. All **433 original assertion occurrences remain in their original relative order**, all **33 inherited failures are resolved**, and **53 additional assertions pass**, with no regression. The original failing assertions' expected values and types are unchanged; export fixtures have only the proven line-ending correction described above.

The 53 additions comprise 36 redirect destination/final-response checks, six classification checks, four download checks, two configuration checks, three legacy-exclusion checks and two anonymous-logout checks. Comparison requires every original occurrence in the same relative order and rejects missing/renamed/reordered assertions, failed additions, empty/inconsistent reports, PHP diagnostics, query errors or missing diagnostic evidence. Thirteen CLI comparison-guard cases pass, including allowed new coverage and rejection of a query error or missing query-error evidence. All **24 runtime checks** pass, including four legacy exclusion probes, and all **seven harness checks** pass. Final lint covers **487 PHP files** with zero syntax failures or compile diagnostics. SHA-256 comparison confirms the candidate's **70 changed PHP/Python source files** match the branch checkout.

Private evidence remains under ignored `data/migration/`:

- `baseline-linux/reports/`: frozen original 433-assertion reference.
- `php84-db114/reports/runtime-checkpoint/`: preserved 433-assertion PHP 8.4 checkpoint with the 33 failures and seven warnings.
- `php84-db114/reports/step1-export-initial/`: original mismatched bytes and line-ending analysis; all eight mismatched files differ only by CRLF conversion.
- `step1/reports/attempt-1/`: first fresh-fixture run, including the two anonymous-logout query errors discovered during review.
- `step1/reports/`: final full-suite, per-request diagnostics, strict export comparisons, runtime/lint checks and comparison guard evidence. Focused reports have group prefixes and do not overwrite the full-suite report.

The older `php84-db114` candidate was stopped with its own Compose project; reports and volumes remain. The ordinary development services were not restarted, and their data was not migrated. `main`, `origin/main` and the branch base remain `54084cc78429efbc0c05f7786110a33309b7cb1f`. Changes are uncommitted; no push, merge, image publication or production operation has been performed. Step 1's exit criterion is satisfied. The next planned work is Step 2 access controls and service boundaries; authentication/schema changes, release packaging/CI and staging/cutover gates remain separate work. This local test checkpoint does not establish production readiness.
