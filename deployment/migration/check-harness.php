<?php
// Focused checks for false-green results and test URL handling; no application or DB bootstrap.
define('BASEPATH', __DIR__);
function log_message($level, $message) {}
function &get_instance() {
    static $instance;
    if ($instance === null) {
        $instance = (object) array('load' => new class {
            public function language($name) {}
        });
    }
    return $instance;
}
require __DIR__ . '/../../relis_sys/libraries/Unit_test.php';
require __DIR__ . '/../../relis_app/helpers/tests/helpers/curl_helper.php';
$checks = 0;
function check($condition, $message) {
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    $checks++;
}
$unit = new CI_Unit_test();
check($unit->last_result() === 'failed', 'Empty test suite must fail.');
$unit->results = array(array('result' => 'passed'));
check($unit->last_result() === 'successful', 'Passing assertion must pass.');
$unit->results[] = array('result' => 'failed');
check($unit->last_result() === 'failed', 'A failed assertion must fail the suite.');
putenv('RELIS_TEST_BASE_URL=http://relis.test/');
$client = new Http_client();
check($client->url('/api/protocol/demoTestProject') === 'http://relis.test/api/protocol/demoTestProject', 'API requests use the isolated host.');
check($client->getShortUrl('http://relis.test/user/login.html') === 'user/login', 'Internal service URL.');
check($client->getShortUrl('http://127.0.0.1:18083/project/index.html?x=1') === 'project/index', 'Loopback URL and query string.');
putenv('RELIS_TEST_BASE_URL=http://localhost:8083/relis/');
$client = new Http_client();
check($client->getShortUrl('http://localhost:8083/relis/user/login.html') === 'user/login', 'Subdirectory installation.');
echo $checks . " harness checks passed.\n";
