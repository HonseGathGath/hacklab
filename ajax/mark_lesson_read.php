<?php
declare(strict_types=1);

require_once __DIR__ . '/../public/bootstrap.php';
require_login();

$input = json_decode(file_get_contents('php://input'), true);
$module = $input['module'] ?? '';

if (!hash_equals($_SESSION['csrf_token'] ?? '', $input['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$stmt = db()->prepare('SELECT id FROM modules WHERE slug = :slug');
$stmt->execute([':slug' => $module]);
$mod = $stmt->fetch();
if (!$mod) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid module']);
    exit;
}

$query = 'INSERT INTO progress (user_id, module_id, lesson_read) VALUES (:user_id, :module_id, 1)';
if (db_runtime_driver() === 'sqlite') {
    $query .= ' ON CONFLICT(user_id, module_id) DO UPDATE SET lesson_read = 1';
} else {
    $query .= ' ON DUPLICATE KEY UPDATE lesson_read = 1';
}
$stmt = db()->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id'], ':module_id' => $mod['id']]);

echo json_encode(['ok' => true]);
