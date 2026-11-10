<?php
// Exercise compatibility boundaries only against a disposable migration fixture.
if (PHP_SAPI !== 'cli' || !getenv('RELIS_MIGRATION_ID') ||
    getenv('RELIS_TEST_BASE_URL') !== 'http://relis.test/' || PHP_VERSION_ID < 80400) {
    fwrite(STDERR, "Requires the isolated PHP 8.4 migration environment.\n");
    exit(2);
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$control = new mysqli('db', 'root', getenv('RELIS_TEST_DB_PASSWORD'), 'relis_db');
$marker = $control->query('SELECT id FROM relis_migration_guard.fixture')->fetch_assoc();
if (!$marker || !hash_equals(getenv('RELIS_MIGRATION_ID'), $marker['id'])) {
    fwrite(STDERR, "Fixture identity mismatch.\n");
    exit(2);
}
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
$checks = array();
function check_runtime($condition, $message) {
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    $checks[] = $message;
}
function login_session_cookie($cookie = '') {
    $headers = array();
    $curl = curl_init('http://relis.test/user/login');
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30,
        CURLOPT_HEADERFUNCTION => function ($handle, $line) use (&$headers) {
            $headers[] = $line;
            return strlen($line);
        }
    ));
    if ($cookie !== '') { curl_setopt($curl, CURLOPT_COOKIE, 'relis_session=' . $cookie); }
    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($body === false || $status !== 200 || preg_match('/Severity:|PHP Error|Deprecated:/', $body)) {
        throw new RuntimeException('Session request failed or emitted a PHP diagnostic.');
    }
    preg_match('/Set-Cookie: relis_session=([^;\r\n]+)([^\r\n]*)/i', implode('', $headers), $match);
    return isset($match[1]) ? array($match[1], $match[2]) : array($cookie, '');
}
list($id, $flags) = login_session_cookie();
check_runtime((bool) preg_match('/^[0-9a-f]{32}$/', $id), 'New HTTP session uses the PHP 8.4 default ID format.');
check_runtime(stripos($flags, 'HttpOnly') !== false && stripos($flags, 'SameSite=Lax') !== false,
    'Session cookie retains HttpOnly and SameSite=Lax.');
list($retained) = login_session_cookie($id);
check_runtime($retained === $id, 'An existing HTTP session survives a second request.');
foreach (array(str_repeat('a', 32), str_repeat('b', 40), 'invalid!session') as $invalid) {
    list($replacement) = login_session_cookie($invalid);
    check_runtime($replacement !== $invalid && (bool) preg_match('/^[0-9a-f]{32}$/', $replacement),
        'Uninitialized, legacy or malformed session ID is replaced: ' . strlen($invalid) . ' bytes.');
}

// Load the real CI driver without booting a web controller. Only our own
// connections are killed, to check both mysqli error-reporting modes.
define('BASEPATH', dirname(__DIR__, 2) . '/relis_sys/');
function log_message($level, $message) {}
function is_php($version) { return version_compare(PHP_VERSION, $version, '>='); }
function &get_instance() {
    static $instance;
    if ($instance === null) { $instance = new stdClass(); }
    return $instance;
}
require BASEPATH . 'core/Model.php';
require dirname(__DIR__, 2) . '/relis_app/models/Screening_dataAccess.php';
$ci = get_instance();
$ci->db_current = (object) array('project' => 'first');
$model = new Screening_dataAccess();
check_runtime($model->db_current === $ci->db_current, 'Screening model delegates its initial DB handle to the controller.');
$ci->db_current = (object) array('project' => 'second');
check_runtime($model->db_current === $ci->db_current, 'Screening model follows controller project switches before local assignment.');
$model->db_current = (object) array('project' => 'local');
check_runtime($model->db_current->project === 'local' && $ci->db_current->project === 'second',
    'An explicitly assigned model DB handle remains local without deprecation.');
require BASEPATH . 'libraries/Pagination.php';
$ci->load = new class { public function language($name) {} };
$ci->lang = new class { public function line($name) { return false; } };
$ci->config = new class { public function item($name) { return false; } };
$ci->uri = new class {
    public $page;
    public function segment($index) { return $this->page; }
};
$pagination = new CI_Pagination(array('base_url' => 'http://relis.test/paper/list',
    'total_rows' => 40, 'per_page' => 10, 'uri_segment' => 3));
foreach (array(array(null, 1), array('10', 2), array('invalid', 1)) as $case) {
    $ci->uri->page = $case[0];
    $pagination->cur_page = 0;
    check_runtime($pagination->create_links() !== '' && $pagination->cur_page === $case[1],
        'Pagination handles missing, numeric or malformed page segments: ' . var_export($case[0], true) . '.');
}
// Verify that legacy edit actions use the paper ID, not the exclusion ID.
// Constructors are bypassed so these controller contracts need no HTTP bootstrap.
require BASEPATH . 'core/Controller.php';
require dirname(__DIR__, 2) . '/relis_app/controllers/Manage.php';
require dirname(__DIR__, 2) . '/relis_app/controllers/relis/Manager.php';
function old_version($text = '') {}
function show_404() { throw new RuntimeException('missing legacy exclusion'); }
class LegacyManageProbe extends Manage {
    public $paper;
    public function __construct() {}
    public function new_exclusion($paper_id, $data = '', $operation = 'new') { $this->paper = $paper_id; }
}
class LegacyRelisManagerProbe extends Manager {
    public $paper;
    public function __construct() {}
    public function new_exclusion($paper_id, $data = array(), $operation = 'new') { $this->paper = $paper_id; }
}
foreach (array(new LegacyManageProbe(), new LegacyRelisManagerProbe()) as $probe) {
    $probe->DBConnection_mdl = new class {
        public $row = array('exclusion_id' => 17, 'exclusion_paper_id' => 1);
        public function get_row_details($table, $id) { return $this->row; }
    };
    $probe->edit_exclusion(17);
    check_runtime($probe->paper === 1, get_class($probe) . ' edits the associated paper when IDs differ.');
    $probe->DBConnection_mdl->row = null;
    $rejected = false;
    try { $probe->edit_exclusion(17); }
    catch (RuntimeException $error) { $rejected = $error->getMessage() === 'missing legacy exclusion'; }
    check_runtime($rejected, get_class($probe) . ' rejects a missing exclusion before using its fields.');
}

require BASEPATH . 'database/DB_driver.php';
class CI_DB extends CI_DB_driver {}
require BASEPATH . 'database/drivers/mysqli/mysqli_driver.php';
foreach (array(MYSQLI_REPORT_OFF, MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT) as $mode) {
    $driver = new CI_DB_mysqli_driver(array('hostname' => 'db', 'username' => 'root',
        'password' => getenv('RELIS_TEST_DB_PASSWORD'), 'database' => 'relis_db', 'db_debug' => false));
    check_runtime($driver->initialize(), 'CI driver connects before liveness check, mode ' . $mode . '.');
    $thread = $driver->conn_id->thread_id;
    $driver->reconnect();
    check_runtime($driver->conn_id !== false && $driver->conn_id->thread_id === $thread,
        'Liveness check retains a working connection, mode ' . $mode . '.');
    $control->query('KILL CONNECTION ' . (int) $thread);
    mysqli_report($mode);
    $driver->reconnect();
    check_runtime($driver->conn_id === false, 'Liveness check invalidates a lost connection, mode ' . $mode . '.');
    check_runtime($driver->initialize(), 'CI driver reinitializes after a lost connection, mode ' . $mode . '.');
    $driver->conn_id->close();
}
$report = array('php_version' => PHP_VERSION, 'status' => 'passed', 'checks' => $checks);
file_put_contents('/migration/runtime-checks.json', json_encode($report, JSON_PRETTY_PRINT));
echo count($checks) . " PHP 8.4 runtime checks passed.\n";
