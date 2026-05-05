<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_login();

$stmt = db()->prepare('SELECT COUNT(*) AS total FROM modules');
$stmt->execute();
$total = (int) $stmt->fetch()['total'];

$stmt = db()->prepare('SELECT COUNT(*) AS completed FROM progress WHERE user_id = :user_id AND lesson_read = 1 AND sandbox_tried = 1 AND quiz_passed = 1');
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$completed = (int) $stmt->fetch()['completed'];

if ($total === 0 || $completed < $total) {
    echo 'Complete all modules to unlock the certificate.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate — HackLab</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="page certificate">
    <main class="certificate__card">
        <h1>HackLab Certified Recruit</h1>
        <p>This certifies that</p>
        <h2><?= e($_SESSION['display_name']) ?></h2>
        <p>has completed all HackLab modules</p>
        <p class="muted">Issued on <?= date('Y-m-d') ?></p>
        <button class="button" onclick="window.print()">Print / Save as PDF</button>
    </main>
</body>
</html>
