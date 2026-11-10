<?php
// Syntax-check application/framework/templates without executing their code.
$root = dirname(__DIR__, 2);
$files = glob($root . '/*.php');
foreach (array('relis_app', 'relis_sys', 'install', 'deployment/migration',
    'deployment/tomcat/testfiles', 'deployment/tomcat/demofiles') as $directory) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
        $root . '/' . $directory, FilesystemIterator::SKIP_DOTS
    ));
    foreach ($iterator as $file) {
        if ($file->isFile() && substr($file->getFilename(), -4) === '.php') {
            $files[] = $file->getPathname();
        }
    }
}
$failures = array();
$diagnostics = array();
foreach (array_unique($files) as $file) {
    $lines = array();
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file) . ' 2>&1', $lines, $status);
    if ($status !== 0) {
        $failures[substr($file, strlen($root) + 1)] = implode("\n", $lines);
    } elseif (preg_grep('/Deprecated:|Warning:/', $lines)) {
        $diagnostics[substr($file, strlen($root) + 1)] = implode("\n", $lines);
    }
}
$report = array('php_version' => PHP_VERSION, 'files' => count(array_unique($files)),
    'syntax_failures' => $failures, 'compile_diagnostics' => $diagnostics);
if (getenv('RELIS_MIGRATION_ID')) {
    file_put_contents('/migration/lint-results.json', json_encode($report, JSON_PRETTY_PRINT));
}
printf("PHP %s: %d files, %d syntax failures, %d files with compile diagnostics.\n",
    PHP_VERSION, $report['files'], count($failures), count($diagnostics));
foreach ($failures + $diagnostics as $file => $message) {
    echo $file . ': ' . $message . "\n";
}
exit(count($failures) || count($diagnostics) ? 1 : 0);
