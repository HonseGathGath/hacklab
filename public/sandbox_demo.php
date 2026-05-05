<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_login();

$module = $_GET['module'] ?? '';
$allowed = ['sqli', 'xss', 'idor'];
if (!in_array($module, $allowed, true)) {
    http_response_code(404);
    echo 'Sandbox not found.';
    exit;
}

$mode = $_GET['mode'] ?? '';
if ($mode !== '' && $mode !== 'vulnerable' && $mode !== 'fixed') {
    http_response_code(400);
    echo 'Invalid mode.';
    exit;
}

$demoPath = realpath(__DIR__ . '/../sandbox/' . $module . '_demo.php');
$sandboxRoot = realpath(__DIR__ . '/../sandbox');
if ($demoPath === false || $sandboxRoot === false || !str_starts_with($demoPath, $sandboxRoot . DIRECTORY_SEPARATOR)) {
    http_response_code(404);
    echo 'Sandbox not found.';
    exit;
}

if ($mode !== '') {
    $_GET['mode'] = $mode;
}

require $demoPath;
