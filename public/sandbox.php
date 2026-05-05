<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_login();

$module = $_GET['module'] ?? '';
$allowed = ['sqli', 'xss', 'idor'];
if (!in_array($module, $allowed, true)) {
    http_response_code(404);
    echo 'Sandbox not found.';
    exit;
}

$moduleRow = null;
try {
    $stmt = db()->prepare('SELECT id FROM modules WHERE slug = :slug');
    $stmt->execute([':slug' => $module]);
    $moduleRow = $stmt->fetch();
    if ($moduleRow) {
        $query = 'INSERT INTO progress (user_id, module_id, sandbox_tried) VALUES (:user_id, :module_id, 1)';
        if (db_runtime_driver() === 'sqlite') {
            $query .= ' ON CONFLICT(user_id, module_id) DO UPDATE SET sandbox_tried = 1';
        } else {
            $query .= ' ON DUPLICATE KEY UPDATE sandbox_tried = 1';
        }
        $stmt = db()->prepare($query);
        $stmt->execute([':user_id' => $_SESSION['user_id'], ':module_id' => $moduleRow['id']]);
    }
} catch (Throwable $exception) {
    error_log('Sandbox progress update failed: ' . $exception->getMessage());
}

$sandboxPath = '../sandbox/' . $module . '_demo.php';
$iframeSrc = $sandboxPath;
if (!empty($_GET['mode'])) {
    $iframeSrc .= '?mode=' . urlencode($_GET['mode']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandbox — HackLab</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/sandbox.css">
</head>
<body class="page">
    <header class="module-header">
        <a href="module.php?slug=<?= e($module) ?>" class="button button--ghost">← Lesson</a>
        <h1>Sandbox: <?= strtoupper(e($module)) ?></h1>
    </header>
    <main class="sandbox">
        <p class="muted">These demos run in isolation. They never touch the main HackLab database.</p>
        <div class="sandbox__panel">
            <iframe class="sandbox__frame" src="<?= e($iframeSrc) ?>"></iframe>
        </div>
        <div class="sandbox__toggle">
            <button class="button" id="toggle-mode" data-mode="vulnerable">Toggle vulnerable/fixed</button>
        </div>
        <section class="sandbox__explain" id="sandbox-explain">
            <h2>What just happened?</h2>
            <p>Try different payloads and compare the vulnerable vs fixed behaviour. Use this to understand how each attack works and why the secure fix prevents it.</p>
        </section>
    </main>
    <script src="../assets/js/sandbox.js"></script>
</body>
</html>
