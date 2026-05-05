-- ============================================================
-- HackLab Main Database Schema
-- ============================================================
CREATE DATABASE IF NOT EXISTS hacklab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hacklab;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    avatar_color CHAR(7) NOT NULL DEFAULT '#00ff9f',
    role ENUM('student','admin') NOT NULL DEFAULT 'student',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS sessions (
    session_token CHAR(64) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (session_token),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
);

CREATE TABLE IF NOT EXISTS modules (
    id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    subtitle VARCHAR(255) NOT NULL,
    owasp_ref VARCHAR(50) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO modules (slug, title, subtitle, owasp_ref, sort_order) VALUES
('sqli', 'SQL Injection', 'Break the query. Own the database.', 'A03:2021 – Injection', 1),
('xss', 'Stored XSS', 'Your input. Everyone\'s problem.', 'A03:2021 – Injection', 2),
('idor', 'IDOR', 'One ID away from someone else\'s data.', 'A01:2021 – Broken Access Control', 3)
ON DUPLICATE KEY UPDATE title = VALUES(title);

CREATE TABLE IF NOT EXISTS progress (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    module_id TINYINT UNSIGNED NOT NULL,
    lesson_read TINYINT(1) NOT NULL DEFAULT 0,
    sandbox_tried TINYINT(1) NOT NULL DEFAULT 0,
    quiz_passed TINYINT(1) NOT NULL DEFAULT 0,
    completed_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_module (user_id, module_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    module_id TINYINT UNSIGNED NOT NULL,
    score TINYINT UNSIGNED NOT NULL,
    max_score TINYINT UNSIGNED NOT NULL DEFAULT 5,
    passed TINYINT(1) NOT NULL,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    INDEX idx_user_module (user_id, module_id)
);
