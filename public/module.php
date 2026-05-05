<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_login();

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT id, slug, title, subtitle, owasp_ref FROM modules WHERE slug = :slug');
$stmt->execute([':slug' => $slug]);
$module = $stmt->fetch();
if (!$module) {
    http_response_code(404);
    echo 'Module not found.';
    exit;
}

$lessonPath = __DIR__ . '/../content/' . $module['slug'] . '_lesson.php';
if (!file_exists($lessonPath)) {
    http_response_code(500);
    echo 'Lesson content missing.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($module['title']) ?> — HackLab</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="page">
    <header class="module-header">
        <a href="dashboard.php" class="button button--ghost">← Dashboard</a>
        <div>
            <h1><?= e($module['title']) ?></h1>
            <p class="muted"><?= e($module['subtitle']) ?></p>
        </div>
        <span class="badge"><?= e($module['owasp_ref']) ?></span>
    </header>
    <main class="lesson">
        <?= csrf_field(); ?>
        <?php include $lessonPath; ?>
        <div class="lesson__actions">
            <button class="button" data-module="<?= e($module['slug']) ?>" id="mark-read">Mark as read</button>
            <a class="button button--ghost" href="sandbox.php?module=<?= e($module['slug']) ?>">Try the sandbox</a>
            <a class="button" href="quiz.php?module=<?= e($module['slug']) ?>">Take the quiz</a>
        </div>
    </main>
    <script src="../assets/js/progress.js"></script>
</body>
</html>
