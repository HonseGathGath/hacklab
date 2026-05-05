<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_login();

$stmt = db()->query('SELECT id, slug, title, subtitle, owasp_ref FROM modules ORDER BY sort_order');
$modules = $stmt->fetchAll();

$progressStmt = db()->prepare('SELECT module_id, lesson_read, sandbox_tried, quiz_passed FROM progress WHERE user_id = :user_id');
$progressStmt->execute([':user_id' => $_SESSION['user_id']]);
$progressRows = $progressStmt->fetchAll();
$progress = [];
foreach ($progressRows as $row) {
    $progress[$row['module_id']] = $row;
}

$completedModules = 0;
foreach ($modules as $module) {
    $row = $progress[$module['id']] ?? ['lesson_read' => 0, 'sandbox_tried' => 0, 'quiz_passed' => 0];
    if ($row['lesson_read'] && $row['sandbox_tried'] && $row['quiz_passed']) {
        $completedModules++;
    }
}
$completionPercent = $modules ? (int) round(($completedModules / count($modules)) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — HackLab</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="page">
    <header class="dashboard-header">
        <div>
            <h1>Bienvenue, <?= e($_SESSION['display_name']) ?></h1>
            <p class="muted">Progression globale: <?= $completionPercent ?>%</p>
        </div>
        <div>
            <a class="button" href="certificate.php">Certificate</a>
            <a class="button button--ghost" href="logout.php">Logout</a>
        </div>
    </header>

    <section class="progress-bar">
        <div class="progress-bar__fill" style="width: <?= $completionPercent ?>%"></div>
    </section>

    <main class="module-grid">
        <?php foreach ($modules as $module):
            $row = $progress[$module['id']] ?? ['lesson_read' => 0, 'sandbox_tried' => 0, 'quiz_passed' => 0];
            $isComplete = $row['lesson_read'] && $row['sandbox_tried'] && $row['quiz_passed'];
        ?>
        <article class="card">
            <div class="card__header">
                <h3><?= e($module['title']) ?></h3>
                <span class="badge"><?= e($module['owasp_ref']) ?></span>
            </div>
            <p><?= e($module['subtitle']) ?></p>
            <ul class="progress-list">
                <li class="<?= $row['lesson_read'] ? 'done' : '' ?>">Lesson</li>
                <li class="<?= $row['sandbox_tried'] ? 'done' : '' ?>">Sandbox</li>
                <li class="<?= $row['quiz_passed'] ? 'done' : '' ?>">Quiz</li>
            </ul>
            <a class="button" href="module.php?slug=<?= e($module['slug']) ?>"><?= $isComplete ? 'Review' : 'Continue' ?></a>
        </article>
        <?php endforeach; ?>
    </main>
    <script src="../assets/js/progress.js"></script>
</body>
</html>
