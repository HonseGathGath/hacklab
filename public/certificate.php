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

$remaining = max(0, $total - $completed);
$completionPercent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
$isUnlocked = $total > 0 && $completed >= $total;
$nextModuleSlug = null;

if (!$isUnlocked && $total > 0) {
    $stmt = db()->prepare(
        'SELECT m.slug
         FROM modules m
         LEFT JOIN progress p ON p.module_id = m.id AND p.user_id = :user_id
         WHERE COALESCE(p.lesson_read, 0) = 0
            OR COALESCE(p.sandbox_tried, 0) = 0
            OR COALESCE(p.quiz_passed, 0) = 0
         ORDER BY m.sort_order ASC
         LIMIT 1'
    );
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $nextModule = $stmt->fetch();
    if ($nextModule) {
        $nextModuleSlug = (string) $nextModule['slug'];
    }
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
    <?php if ($isUnlocked): ?>
        <main class="certificate__card">
            <h1>HackLab Certified Recruit</h1>
            <p>This certifies that</p>
            <h2><?= e($_SESSION['display_name']) ?></h2>
            <p>has completed all HackLab modules</p>
            <p class="muted">Issued on <?= date('Y-m-d') ?></p>
            <div class="certificate__actions">
                <button class="button" onclick="window.print()">Print / Save as PDF</button>
                <a class="button button--ghost" href="dashboard.php">Back to dashboard</a>
            </div>
        </main>
    <?php else: ?>
        <main class="certificate__card certificate__card--locked">
            <span class="certificate__status">Certificate locked</span>
            <h1>Keep training to unlock your certificate</h1>
            <p>You need every module marked complete (lesson, sandbox, and quiz) before certification is available.</p>
            <div class="certificate__progress" aria-label="Training progress">
                <div class="certificate__progress-fill" style="width: <?= $completionPercent ?>%"></div>
            </div>
            <p class="muted"><?= $completed ?> / <?= $total ?> modules completed (<?= $completionPercent ?>%)</p>
            <p class="muted"><?= $remaining ?> module<?= $remaining === 1 ? '' : 's' ?> remaining.</p>
            <div class="certificate__actions">
                <?php if ($nextModuleSlug !== null): ?>
                    <a class="button" href="module.php?slug=<?= e($nextModuleSlug) ?>">Continue training</a>
                <?php endif; ?>
                <a class="button button--ghost" href="dashboard.php">Back to dashboard</a>
            </div>
        </main>
    <?php endif; ?>
</body>
</html>
