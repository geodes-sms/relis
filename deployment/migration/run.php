<?php
// Run only inside the disposable migration application's container.
if (PHP_SAPI !== 'cli' || !getenv('RELIS_MIGRATION_ID') ||
    getenv('RELIS_TEST_BASE_URL') !== 'http://relis.test/') {
    fwrite(STDERR, "Refusing tests outside the isolated migration environment.\n");
    exit(2);
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli('db', 'root', getenv('RELIS_TEST_DB_PASSWORD'), 'relis_db');
$marker = $db->query('SELECT id FROM relis_migration_guard.fixture')->fetch_assoc();
if (!$marker || !hash_equals(getenv('RELIS_MIGRATION_ID'), $marker['id'])) {
    fwrite(STDERR, "Fixture database identity does not match. No tests executed.\n");
    exit(2);
}

function request($path, $post = null, $cookies = false)
{
    $curl = curl_init(getenv('RELIS_TEST_BASE_URL') . $path);
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 900
    ));
    if ($cookies) {
        curl_setopt($curl, CURLOPT_COOKIEJAR, '/migration/bootstrap-cookies.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, '/migration/bootstrap-cookies.txt');
    }
    if ($post !== null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }
    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($body === false || $status >= 400) {
        file_put_contents('/migration/request-error.html', (string) $body);
        throw new RuntimeException('HTTP request failed: ' . $path . ' (' . $status . ') ' . curl_error($curl));
    }
    curl_close($curl);
    return $body;
}

$mode = $argv[1] ?? 'test';
if ($mode === 'bootstrap') {
    $existing = $db->query("SELECT project_id FROM projects WHERE project_label='demo_relis' AND project_active=1")->fetch_assoc();
    $schemaExists = (int) $db->query("SELECT COUNT(*) AS n FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='relis_dev_correct_demo_relis'")->fetch_assoc()['n'];
    if (!$schemaExists) {
        // The repository seed advertises the demo project but does not create its DB.
        // Only repair this fresh, guarded fixture; never replace an installed demo DB.
        if ((int) $db->query("SELECT COUNT(*) AS n FROM projects WHERE project_label<>'demo_relis'")->fetch_assoc()['n'] !== 0) {
            throw new RuntimeException('Bootstrap requires a fresh fixture with no other projects.');
        }
        if ($existing) {
            $projectId = (int) $existing['project_id'];
            $db->query('DELETE FROM userproject WHERE project_id=' . $projectId);
            $db->query('DELETE FROM projects WHERE project_id=' . $projectId);
            $db->query('ALTER TABLE projects AUTO_INCREMENT=1');
        }
        request('user/check_form', http_build_query(array('user_username' => 'admin', 'user_password' => '123')), true);
        $body = request('project/save_new_project', array(
            'install_config' => new CURLFile(getcwd() . '/deployment/tomcat/demofiles/classification_install_demo_relis.php')
        ), true);
        file_put_contents('/migration/bootstrap.html', $body);
        $existing = $db->query("SELECT project_id FROM projects WHERE project_label='demo_relis' AND project_active=1")->fetch_assoc();
        if (!$existing) {
            throw new RuntimeException('Demo project bootstrap did not complete; inspect bootstrap.html.');
        }
        $projectId = (int) $existing['project_id'];
        $db->query("INSERT INTO userproject (user_id,project_id,user_role,added_by) VALUES (2," . $projectId . ",'Reviewer',2)");
    }
    $paperTable = (int) $db->query("SELECT COUNT(*) AS n FROM information_schema.TABLES WHERE TABLE_SCHEMA='relis_dev_correct_demo_relis' AND TABLE_NAME='paper'")->fetch_assoc()['n'];
    if (!$existing || !$paperTable || !is_file('relis_app/libraries/table_config/project/install_config_demo_relis.php')) {
        throw new RuntimeException('Demo fixture is incomplete; database tables and generated configuration are required.');
    }
    echo "Demo fixture ready.\n";
    exit(0);
}
if ($mode !== 'test') {
    throw new InvalidArgumentException('Use bootstrap or test.');
}
$group = $argv[2] ?? 'all';
$groups = array('all', 'user', 'project', 'paper', 'screening', 'quality_assessment',
    'data_extraction', 'reporting', 'element', 'home', 'install', 'admin', 'config',
    'manage', 'manager', 'relismanager', 'api', 'apiquery', 'op', 'inclusion_mode_conflict');
if (!in_array($group, $groups, true)) {
    throw new InvalidArgumentException('Unknown test group.');
}
$reportPrefix = '/migration/' . ($group === 'all' ? '' : $group . '-');
$logOffsets = array();
foreach (glob('relis_app/logs/log-*.php') as $logFile) {
    $logOffsets[$logFile] = filesize($logFile);
}
$startedAt = gmdate('Y-m-d H:i:s');
$body = request('test/unit_test/relis_unit_test/json_report/' . $group);
clearstatcache();
$logDelta = '';
foreach (glob('relis_app/logs/log-*.php') as $logFile) {
    $logDelta .= substr(file_get_contents($logFile), $logOffsets[$logFile] ?? 0);
}
file_put_contents($reportPrefix . 'application-log-delta.txt', $logDelta);
preg_match_all('/ --> Severity: ([^\r\n]+)/', $logDelta, $loggedDiagnostics);
$diagnosticCounts = array_count_values($loggedDiagnostics[1]);
ksort($diagnosticCounts);
file_put_contents($reportPrefix . 'runtime-diagnostics.json', json_encode($diagnosticCounts, JSON_PRETTY_PRINT));
file_put_contents($reportPrefix . 'test-response.txt', $body);
$report = json_decode($body, true);
if (!is_array($report) || !isset($report['total'], $report['results'], $report['html_report'])) {
    fwrite(STDERR, "Test endpoint did not return a complete JSON report; inspect test-response.txt.\n");
    exit(2);
}
file_put_contents($reportPrefix . 'test-report.html', $report['html_report']);
file_put_contents($reportPrefix . 'test-diagnostics.html', $report['diagnostics']);
unset($report['html_report']);
$report['fixture_id'] = $marker['id'];
$report['database_version'] = $db->server_info;
$report['started_at_utc'] = $startedAt;
$report['logged_php_diagnostic_count'] = count($loggedDiagnostics[1]);
$report['logged_query_error_count'] = substr_count($logDelta, ' --> Query error:');
file_put_contents($reportPrefix . 'test-results.json', json_encode($report, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE));
printf("PHP %s / MariaDB %s: %d assertions, %d passed, %d failed (%.2fs).\n",
    $report['php_version'], $report['database_version'], $report['total'],
    $report['passed'], $report['failed'], $report['duration_seconds']);
$consistent = $report['total'] > 0 && $report['total'] === count($report['results']) &&
    $report['total'] === $report['passed'] + $report['failed'];
exit($consistent && $report['status'] === 'successful' && $report['failed'] === 0 &&
    $report['diagnostics'] === '' && count($loggedDiagnostics[1]) === 0 &&
    $report['logged_query_error_count'] === 0 ? 0 : 1);
