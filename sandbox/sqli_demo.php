<?php
declare(strict_types=1);

// SQLite in-memory demo
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)');
$db->exec("INSERT INTO users (username, password) VALUES ('admin', 'admin123'), ('student', 'letmein')");

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$mode = $_POST['mode'] ?? ($_GET['mode'] ?? 'vulnerable');
$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";

$resultRows = [];
$error = '';
try {
    if ($mode === 'fixed') {
        $stmt = $db->prepare('SELECT * FROM users WHERE username = :username AND password = :password');
        $stmt->execute([':username' => $username, ':password' => $password]);
        $resultRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $resultRows = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SQLi Demo</title>
    <style>
        body { font-family: monospace; background: #0a0a0f; color: #00ff9f; padding: 16px; }
        input, button { margin: 4px 0; padding: 6px; }
        pre { background: #12121a; padding: 8px; }
    </style>
</head>
<body>
    <h3>SQL Injection Sandbox</h3>
    <form method="post">
        <label>Username <input type="text" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES) ?>"></label><br>
        <label>Password <input type="text" name="password" value="<?= htmlspecialchars($password, ENT_QUOTES) ?>"></label><br>
        <input type="hidden" name="mode" value="<?= htmlspecialchars($mode, ENT_QUOTES) ?>" id="mode">
        <button type="submit">Run query</button>
        <button type="button" onclick="toggleMode()">Toggle mode</button>
    </form>
    <p>Mode: <strong id="mode-label"><?= htmlspecialchars($mode, ENT_QUOTES) ?></strong></p>
    <pre><?= htmlspecialchars($query, ENT_QUOTES) ?></pre>
    <?php if ($error): ?>
        <p>Error: <?= htmlspecialchars($error, ENT_QUOTES) ?></p>
    <?php endif; ?>
    <pre><?= htmlspecialchars(json_encode($resultRows, JSON_PRETTY_PRINT), ENT_QUOTES) ?></pre>
    <script>
        function toggleMode() {
            const modeInput = document.getElementById('mode');
            modeInput.value = modeInput.value === 'vulnerable' ? 'fixed' : 'vulnerable';
            document.getElementById('mode-label').textContent = modeInput.value;
        }
    </script>
</body>
</html>
