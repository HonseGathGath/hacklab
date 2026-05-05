<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — HackLab</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="page">
    <main class="auth">
        <h1>Contact the team</h1>
        <?php if ($success): ?>
            <div class="alert">Message sent. We'll reply soon.</div>
        <?php endif; ?>
        <form method="post" class="form">
            <?= csrf_field(); ?>
            <label>Name
                <input type="text" name="name" required>
            </label>
            <label>Email
                <input type="email" name="email" required>
            </label>
            <label>Topic
                <select name="topic" required>
                    <option value="">Choose a topic</option>
                    <option value="modules">Modules</option>
                    <option value="account">Account</option>
                    <option value="security">Security</option>
                </select>
            </label>
            <label>Message
                <textarea name="message" rows="4" required></textarea>
            </label>
            <label>
                <input type="checkbox" name="newsletter"> Subscribe to updates
            </label>
            <button class="button" type="submit">Send</button>
        </form>
        <p><a href="index.php">Back to home</a></p>
    </main>
</body>
</html>
