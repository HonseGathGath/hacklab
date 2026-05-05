<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Jeton CSRF invalide.';
    } else {
        try {
            $errors = register_user(
                trim($_POST['username'] ?? ''),
                trim($_POST['email'] ?? ''),
                trim($_POST['display_name'] ?? ''),
                (string) ($_POST['password'] ?? '')
            );
            if (!$errors) {
                redirect('login.php');
            }
        } catch (Throwable $exception) {
            http_response_code(500);
            error_log('Registration failed: ' . exception_log_message($exception));
            $errors = [friendly_service_error($exception)];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — HackLab</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="page">
    <main class="auth">
        <h1>Create your recruit profile</h1>
        <?php if ($errors): ?>
            <div class="alert alert--error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" class="form">
            <?= csrf_field(); ?>
            <label>Username
                <input type="text" name="username" required>
            </label>
            <label>Email
                <input type="email" name="email" required>
            </label>
            <label>Display name
                <input type="text" name="display_name" required>
            </label>
            <label>Password
                <input type="password" name="password" minlength="8" required>
            </label>
            <button class="button" type="submit">Register</button>
        </form>
        <p>Already registered? <a href="login.php">Login</a></p>
    </main>
</body>
</html>
