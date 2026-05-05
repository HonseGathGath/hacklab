<?php
declare(strict_types=1);

$root = __DIR__;
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = $uri === null ? '/' : $uri;
$uri = rawurldecode($uri);

$baseUrl = '';
if (is_file($root . '/includes/config.php')) {
    require $root . '/includes/config.php';
    if (defined('BASE_URL')) {
        $baseUrl = (string) BASE_URL;
    }
}
$baseUrl = '/' . trim($baseUrl, '/');
if ($baseUrl === '/') {
    $baseUrl = '';
}

if ($baseUrl !== '' && $uri === '/') {
    header('Location: ' . $baseUrl . '/');
    return true;
}

$blockedPrefixes = ['/includes', '/sandbox', '/sql'];
foreach ($blockedPrefixes as $blockedPrefix) {
    if ($uri === $blockedPrefix || str_starts_with($uri, $blockedPrefix . '/')) {
        http_response_code(403);
        echo 'Forbidden';
        return true;
    }
}

$normalizedUri = $uri === '' ? '/' : $uri;
$sensitiveNames = [
    '/.htaccess',
    '/.env',
    '/run.sh',
    '/router.php',
    '/README.md',
    '/report.md',
];
if (preg_match('#(^|/)\.[^/]+#', $normalizedUri) === 1) {
    http_response_code(404);
    echo 'Not Found';
    return true;
}
foreach ($sensitiveNames as $sensitiveName) {
    if ($normalizedUri === $sensitiveName) {
        http_response_code(404);
        echo 'Not Found';
        return true;
    }
}

$staticPrefixes = ['/assets', '/ajax'];
foreach ($staticPrefixes as $staticPrefix) {
    if ($normalizedUri === $staticPrefix || str_starts_with($normalizedUri, $staticPrefix . '/')) {
        $staticPath = $root . $normalizedUri;
        if (is_file($staticPath)) {
            return false;
        }
        break;
    }
}

$path = $uri;
if ($baseUrl !== '' && ($uri === $baseUrl || str_starts_with($uri, $baseUrl . '/'))) {
    $path = substr($uri, strlen($baseUrl));
    if ($path === '') {
        $path = '/';
    }
}

if ($path === '/' || $path === '') {
    $path = '/index.php';
}

$publicPath = $root . '/public' . $path;
if (is_file($publicPath)) {
    if (str_ends_with($publicPath, '.php')) {
        require $publicPath;
        return true;
    }

    http_response_code(404);
    echo 'Not Found';
    return true;
}

http_response_code(404);
echo 'Not Found';
return true;
