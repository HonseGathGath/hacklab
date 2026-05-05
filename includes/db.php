<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

class DatabaseException extends RuntimeException
{
}

function db_driver(): string
{
    $driver = strtolower((string) (getenv('HACKLAB_DB_DRIVER') ?: DB_DRIVER));
    return $driver === 'sqlite' ? 'sqlite' : 'mysql';
}

function db_runtime_driver(): string
{
    $runtime = $GLOBALS['hacklab_db_runtime_driver'] ?? null;
    if (is_string($runtime) && $runtime !== '') {
        return $runtime;
    }

    return db_driver();
}

function db_available_driver(string $preferred): string
{
    $drivers = PDO::getAvailableDrivers();
    if (in_array($preferred, $drivers, true)) {
        return $preferred;
    }

    if ($preferred !== 'sqlite' && in_array('sqlite', $drivers, true)) {
        return 'sqlite';
    }

    throw new RuntimeException(
        sprintf(
            'Database driver "%s" not available and no fallback driver installed. Available drivers: %s',
            $preferred,
            implode(', ', $drivers)
        )
    );
}

function db_sqlite_path(): string
{
    $path = (string) (getenv('HACKLAB_SQLITE_PATH') ?: DB_SQLITE_PATH);
    if ($path === '') {
        throw new RuntimeException('SQLite path is empty.');
    }

    return $path;
}

function db_bootstrap_sqlite_schema(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        display_name TEXT NOT NULL,
        avatar_color TEXT NOT NULL DEFAULT \'#00ff9f\',
        role TEXT NOT NULL DEFAULT \'student\',
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        last_login TEXT NULL
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS sessions (
        session_token TEXT NOT NULL PRIMARY KEY,
        user_id INTEGER NOT NULL,
        ip_address TEXT NOT NULL,
        user_agent TEXT NOT NULL,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        expires_at TEXT NOT NULL,
        is_active INTEGER NOT NULL DEFAULT 1,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS modules (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT NOT NULL UNIQUE,
        title TEXT NOT NULL,
        subtitle TEXT NOT NULL,
        owasp_ref TEXT NOT NULL,
        sort_order INTEGER NOT NULL
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS progress (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        module_id INTEGER NOT NULL,
        lesson_read INTEGER NOT NULL DEFAULT 0,
        sandbox_tried INTEGER NOT NULL DEFAULT 0,
        quiz_passed INTEGER NOT NULL DEFAULT 0,
        completed_at TEXT NULL,
        UNIQUE(user_id, module_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS quiz_attempts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        module_id INTEGER NOT NULL,
        score INTEGER NOT NULL,
        max_score INTEGER NOT NULL DEFAULT 5,
        passed INTEGER NOT NULL,
        attempted_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
    )');

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO modules (slug, title, subtitle, owasp_ref, sort_order) VALUES (:slug, :title, :subtitle, :owasp_ref, :sort_order)');
    $seed = [
        ['slug' => 'sqli', 'title' => 'SQL Injection', 'subtitle' => 'Break the query. Own the database.', 'owasp_ref' => 'A03:2021 – Injection', 'sort_order' => 1],
        ['slug' => 'xss', 'title' => 'Stored XSS', 'subtitle' => 'Your input. Everyone\'s problem.', 'owasp_ref' => 'A03:2021 – Injection', 'sort_order' => 2],
        ['slug' => 'idor', 'title' => 'IDOR', 'subtitle' => 'One ID away from someone else\'s data.', 'owasp_ref' => 'A01:2021 – Broken Access Control', 'sort_order' => 3],
    ];
    foreach ($seed as $row) {
        $stmt->execute([
            ':slug' => $row['slug'],
            ':title' => $row['title'],
            ':subtitle' => $row['subtitle'],
            ':owasp_ref' => $row['owasp_ref'],
            ':sort_order' => $row['sort_order'],
        ]);
    }
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $driver = db_available_driver(db_driver());
    $GLOBALS['hacklab_db_runtime_driver'] = $driver;
    try {
        if ($driver === 'sqlite') {
            $pdo = db_sqlite_connect();
            return $pdo;
        }

        $host = (string) (getenv('HACKLAB_DB_HOST') ?: DB_HOST);
        $name = (string) (getenv('HACKLAB_DB_NAME') ?: DB_NAME);
        $user = (string) (getenv('HACKLAB_DB_USER') ?: DB_USER);
        $pass = (string) (getenv('HACKLAB_DB_PASS') ?: DB_PASS);
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);
        $pdo = new PDO(
            $dsn,
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        $drivers = PDO::getAvailableDrivers();
        if ($driver === 'mysql' && in_array('sqlite', $drivers, true)) {
            error_log('MySQL unavailable, falling back to SQLite: ' . $exception->getMessage());
            $GLOBALS['hacklab_db_runtime_driver'] = 'sqlite';
            try {
                $pdo = db_sqlite_connect();
            } catch (Throwable $fallbackException) {
                throw new DatabaseException('Database fallback connection failed.', 0, $fallbackException);
            }

            return $pdo;
        }

        throw new DatabaseException('Database connection failed.', 0, $exception);
    } catch (RuntimeException $exception) {
        throw new DatabaseException('Database setup failed.', 0, $exception);
    }

    return $pdo;
}

function db_sqlite_connect(): PDO
{
    $sqlitePath = db_sqlite_path();
    $directory = dirname($sqlitePath);
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Unable to create SQLite directory: ' . $directory);
    }

    $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    db_bootstrap_sqlite_schema($pdo);

    return $pdo;
}
