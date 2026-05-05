<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT id, username, display_name, password_hash, role FROM users WHERE username = :username');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['display_name'] = $user['display_name'];
    $_SESSION['role'] = $user['role'];

    $token = bin2hex(random_bytes(32));
    $stmt = db()->prepare('INSERT INTO sessions (session_token, user_id, ip_address, user_agent, expires_at) VALUES (:token, :user_id, :ip, :ua, :expires)');
    $stmt->execute([
        ':token' => hash('sha256', $token),
        ':user_id' => $user['id'],
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ':expires' => date('Y-m-d H:i:s', strtotime('+7 days')),
    ]);

    setcookie(SESSION_COOKIE_NAME, $token, [
        'expires' => time() + 60 * 60 * 24 * 7,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    return true;
}

function register_user(string $username, string $email, string $displayName, string $password): array
{
    $errors = [];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email invalide.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
    }

    if ($errors) {
        return $errors;
    }

    $stmt = db()->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
    $stmt->execute([':username' => $username, ':email' => $email]);
    if ($stmt->fetch()) {
        return ['Nom d\'utilisateur ou email deja utilise.'];
    }

    $stmt = db()->prepare('INSERT INTO users (username, email, password_hash, display_name) VALUES (:username, :email, :hash, :display_name)');
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':hash' => password_hash($password, PASSWORD_BCRYPT),
        ':display_name' => $displayName,
    ]);

    return [];
}

function logout(): void
{
    if (!empty($_COOKIE[SESSION_COOKIE_NAME])) {
        $stmt = db()->prepare('UPDATE sessions SET is_active = 0 WHERE session_token = :token');
        $stmt->execute([':token' => hash('sha256', $_COOKIE[SESSION_COOKIE_NAME])]);
    }

    $_SESSION = [];
    session_destroy();
    setcookie(SESSION_COOKIE_NAME, '', time() - 3600, '/');
}

function session_guard(): void
{
    if (!is_logged_in() || empty($_COOKIE[SESSION_COOKIE_NAME])) {
        return;
    }

    $stmt = db()->prepare('SELECT user_id, expires_at, is_active FROM sessions WHERE session_token = :token');
    $stmt->execute([':token' => hash('sha256', $_COOKIE[SESSION_COOKIE_NAME])]);
    $session = $stmt->fetch();

    if (!$session || !$session['is_active'] || strtotime($session['expires_at']) < time()) {
        logout();
    }
}
