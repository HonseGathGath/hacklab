<?php
declare(strict_types=1);

require_once __DIR__ . '/../public/bootstrap.php';
require_login();

$stmt = db()->prepare('SELECT module_id, lesson_read, sandbox_tried, quiz_passed FROM progress WHERE user_id = :user_id');
$stmt->execute([':user_id' => $_SESSION['user_id']]);

echo json_encode($stmt->fetchAll());
