<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

set_exception_handler(function (Throwable $exception): void {
    if (headers_sent()) {
        error_log('Unhandled exception: ' . $exception->getMessage());
        return;
    }

    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isJson = stripos($accept, 'application/json') !== false
        || stripos($contentType, 'application/json') !== false
        || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

    http_response_code(500);
    error_log(sprintf(
        'Unhandled exception %s: %s in %s:%d',
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));

    if ($isJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Internal server error']);
        return;
    }

    $baseUrl = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '';
    $cssPath = $baseUrl === '' ? '/assets/css/main.css' : $baseUrl . '/assets/css/main.css';
    $safeCssPath = htmlspecialchars($cssPath, ENT_QUOTES, 'UTF-8');
    echo '<!DOCTYPE html><html lang="fr"><head>'
        . '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
        . '<title>Erreur serveur — HackLab</title>'
        . '<link rel="stylesheet" href="' . $safeCssPath . '">'
        . '</head><body class="page">'
        . '<main class="auth"><h1>Server error</h1>'
        . '<p>Une erreur est survenue. Reessayez plus tard.</p>'
        . '</main></body></html>';
    exit(1);
});

session_guard();
