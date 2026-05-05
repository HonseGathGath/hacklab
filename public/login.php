<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Jeton CSRF invalide.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        try {
            if (!login($username, $password)) {
                $error = 'Identifiants invalides.';
            } else {
                redirect('dashboard.php');
            }
        } catch (Throwable $exception) {
            http_response_code(500);
            error_log('Login failed: ' . exception_log_message($exception));
            $error = friendly_service_error($exception);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — HackLab</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="page">
    <main class="auth">
        <h1>Welcome back, recruit</h1>
        <?php if ($error): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" class="form">
            <?= csrf_field(); ?>
            <label>Username
                <input type="text" name="username" required>
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <button class="button" type="submit">Login</button>
        </form>
        <p>No account yet? <a href="register.php">Register</a></p>
    </main>
</body>
</html>
