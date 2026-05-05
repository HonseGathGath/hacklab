<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function is_db_exception(Throwable $exception): bool
{
    if ($exception instanceof PDOException) {
        return true;
    }

    if ($exception instanceof DatabaseException) {
        return true;
    }

    if ($exception instanceof RuntimeException) {
        $message = $exception->getMessage();
        if (str_contains($message, 'Database driver')) {
            return true;
        }
    }

    $previous = $exception->getPrevious();
    if ($previous instanceof Throwable) {
        return is_db_exception($previous);
    }

    return false;
}

function friendly_service_error(Throwable $exception): string
{
    if (is_db_exception($exception)) {
        return 'Base de donnees indisponible temporairement.';
    }

    return 'Service indisponible. Veuillez reessayer.';
}

function exception_log_message(Throwable $exception): string
{
    $messages = [$exception->getMessage()];

    while ($exception = $exception->getPrevious()) {
        $messages[] = $exception->getMessage();
    }

    return implode(' | Caused by: ', $messages);
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function base_url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = '/' . ltrim($path, '/');

    return $base === '' ? $path : $base . $path;
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect(base_url('login.php'));
    }
}

function require_admin(): void
{
    if (!is_logged_in() || ($_SESSION['role'] ?? '') !== 'admin') {
        redirect(base_url('login.php'));
    }
}
