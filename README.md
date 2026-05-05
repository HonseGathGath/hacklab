# HackLab — LAMP Cybersecurity Training Platform

## Overview
HackLab is a dark-themed, CTF-inspired cybersecurity education platform built on a native LAMP stack. It teaches SQLi, XSS, and IDOR with lesson pages, safe sandboxes, quizzes, and progress tracking.

## Requirements
- Apache 2.4+ with `mod_rewrite`
- PHP 8.0+ with extensions: `pdo`, `pdo_mysql`, `pdo_sqlite`, `session`
- MariaDB/MySQL 8+

## Setup (Arch Linux / Native LAMP)
1. Install packages:
   ```bash
   sudo pacman -S apache mariadb php php-apache
   sudo systemctl enable --now httpd mariadb
   ```
2. Create database and tables:
   ```bash
   mysql -u root -p < sql/schema.sql
   ```
3. Configure credentials:
   ```bash
   cp includes/config.example.php includes/config.php
   ```
   Edit `includes/config.php` with DB host/user/password.
4. Place project under `/srv/http/hacklab` and set Apache `DocumentRoot` to `/srv/http/hacklab/public`.
5. Visit `http://localhost/hacklab` and register.

## Quick Start (PHP built-in server)
If you want a lightweight local run without Apache, you can use the provided script:

```bash
./run.sh
```

This starts `php -S` on `127.0.0.1:8000` and serves from the repo root using
`router.php` so `/hacklab` works with the existing `BASE_URL` setting.
Override with `HOST` and `PORT`:

```bash
HOST=0.0.0.0 PORT=8080 ./run.sh
```

The dev router only exposes `/public`, `/assets`, and `/ajax` content. Direct access
to repo files (like `.htaccess` or `run.sh`) is blocked.

## Security Notes
- All queries use PDO prepared statements.
- Output uses `htmlspecialchars()`.
- CSRF tokens on forms.
- Session table to track logins.

## Sandbox Isolation
Sandbox demos use SQLite in-memory databases or hardcoded data. They never access the main MySQL database.
