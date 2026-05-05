<?php
declare(strict_types=1);

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE comments (id INTEGER PRIMARY KEY, body TEXT)');

$mode = $_POST['mode'] ?? ($_GET['mode'] ?? 'vulnerable');
if (!empty($_POST['comment'])) {
    $stmt = $db->prepare('INSERT INTO comments (body) VALUES (:body)');
    $stmt->execute([':body' => $_POST['comment']]);
}

$comments = $db->query('SELECT body FROM comments')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>XSS Demo</title>
    <style>
        body { font-family: monospace; background: #0a0a0f; color: #00ff9f; padding: 16px; }
        textarea { width: 100%; min-height: 80px; margin: 8px 0; }
        .panel { background: #12121a; padding: 8px; margin-top: 10px; }
    </style>
</head>
<body>
    <h3>XSS Sandbox</h3>
    <form method="post">
        <textarea name="comment" placeholder="Try <script>alert('XSS')</script>"></textarea>
        <input type="hidden" name="mode" id="mode" value="<?= htmlspecialchars($mode, ENT_QUOTES) ?>">
        <button type="submit">Post</button>
        <button type="button" onclick="toggleMode()">Toggle mode</button>
    </form>
    <p>Mode: <strong id="mode-label"><?= htmlspecialchars($mode, ENT_QUOTES) ?></strong></p>
    <div class="panel">
        <h4>Rendered output</h4>
        <?php foreach ($comments as $comment): ?>
            <div>
                <?php if ($mode === 'vulnerable'): ?>
                    <?= $comment['body'] ?>
                <?php else: ?>
                    <?= htmlspecialchars($comment['body'], ENT_QUOTES) ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="panel">
        <h4>Stored raw</h4>
        <pre><?= htmlspecialchars(json_encode($comments, JSON_PRETTY_PRINT), ENT_QUOTES) ?></pre>
    </div>
    <script>
        function toggleMode() {
            const modeInput = document.getElementById('mode');
            modeInput.value = modeInput.value === 'vulnerable' ? 'fixed' : 'vulnerable';
            document.getElementById('mode-label').textContent = modeInput.value;
        }
    </script>
</body>
</html>
