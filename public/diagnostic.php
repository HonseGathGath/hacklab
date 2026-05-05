<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    db()->query('SELECT 1');
    echo 'PHP OK | DB OK';
} catch (Throwable $e) {
    error_log('Diagnostic DB error: ' . $e->getMessage());
    echo 'PHP OK | DB ERROR';
}
