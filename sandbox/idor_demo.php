<?php
declare(strict_types=1);

$files = [
    1 => ['owner' => 'you', 'title' => 'Recruit Orders', 'content' => 'Welcome recruit. Study the materials.'],
    2 => ['owner' => 'you', 'title' => 'Sandbox Notes', 'content' => 'You have access to this file.'],
    3 => ['owner' => 'other', 'title' => 'Confidential Report', 'content' => 'Top secret intel belonging to someone else.'],
    4 => ['owner' => 'other', 'title' => 'Private Key', 'content' => 'This should not be visible to you.'],
    5 => ['owner' => 'other', 'title' => 'Incident Log', 'content' => 'Internal security incident.'],
];

$fileId = (int) ($_GET['file_id'] ?? 1);
$file = $files[$fileId] ?? null;
$isAuthorized = $file && $file['owner'] === 'you';
$mode = $_GET['mode'] ?? 'vulnerable';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>IDOR Demo</title>
    <style>
        body { font-family: monospace; background: #0a0a0f; color: #00ff9f; padding: 16px; }
        .panel { background: #12121a; padding: 8px; margin-top: 10px; }
        input { padding: 4px; }
    </style>
</head>
<body>
    <h3>IDOR Sandbox</h3>
    <form method="get">
        <label>File ID <input type="number" name="file_id" value="<?= htmlspecialchars((string) $fileId, ENT_QUOTES) ?>" min="1" max="5"></label>
        <button type="submit">Load</button>
    </form>
    <div class="panel">
        <h4><?= $mode === 'fixed' ? 'Fixed access (authorization)' : 'Vulnerable access (no check)' ?></h4>
        <?php if ($mode === 'fixed'): ?>
            <?php if ($file && $isAuthorized): ?>
                <p><strong><?= htmlspecialchars($file['title'], ENT_QUOTES) ?></strong></p>
                <p><?= htmlspecialchars($file['content'], ENT_QUOTES) ?></p>
            <?php elseif ($file): ?>
                <p>Access denied. You do not own this file.</p>
            <?php else: ?>
                <p>File not found.</p>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($file): ?>
                <p><strong><?= htmlspecialchars($file['title'], ENT_QUOTES) ?></strong></p>
                <p><?= htmlspecialchars($file['content'], ENT_QUOTES) ?></p>
            <?php else: ?>
                <p>File not found.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
