#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

HOST="${HOST:-127.0.0.1}"
PORT="${PORT:-8000}"

if [[ "${1:-}" == "-h" || "${1:-}" == "--help" ]]; then
  cat <<'EOF'
Usage: ./run.sh

Starts the HackLab dev server with PHP's built-in server.

Environment:
  HOST (default 127.0.0.1)
  PORT (default 8000)

Example:
  HOST=0.0.0.0 PORT=8080 ./run.sh
EOF
  exit 0
fi

if ! command -v php >/dev/null 2>&1; then
  echo "Error: php is not installed or not in PATH."
  exit 1
fi

PHP_OPTS=()
PDO_DRIVERS="$(php -r 'echo implode(",", PDO::getAvailableDrivers());' 2>/dev/null || true)"
if [[ -z "$PDO_DRIVERS" ]]; then
  EXT_DIR="$(php -i | awk -F'=> ' '/^extension_dir =>/{print $2; exit}' | xargs || true)"
  if [[ -n "$EXT_DIR" ]]; then
    if [[ -f "$EXT_DIR/pdo_mysql.so" ]]; then
      PHP_OPTS+=("-d" "extension=pdo_mysql")
    fi
    if [[ -f "$EXT_DIR/pdo_sqlite.so" ]]; then
      PHP_OPTS+=("-d" "extension=pdo_sqlite")
    fi
    if [[ -f "$EXT_DIR/sqlite3.so" ]]; then
      PHP_OPTS+=("-d" "extension=sqlite3")
    fi
  fi

  if [[ ${#PHP_OPTS[@]} -gt 0 ]]; then
    echo "No PDO drivers detected. Enabling available PDO modules for dev server: ${PHP_OPTS[*]}"
  else
    echo "Warning: No PDO drivers detected and no PDO extension modules found in extension_dir."
    echo "Login will fail until pdo_mysql or pdo_sqlite is enabled in php.ini."
  fi
fi

if [[ ! -f "includes/config.php" ]]; then
  if [[ -f "includes/config.example.php" ]]; then
    cp "includes/config.example.php" "includes/config.php"
    echo "Created includes/config.php from example. Update DB credentials before logging in."
  else
    echo "Error: includes/config.php missing and no example to copy."
    exit 1
  fi
fi

BASE_PATH="$(php -r "if (is_file('includes/config.php')) { require 'includes/config.php'; echo defined('BASE_URL') ? (string) BASE_URL : ''; }" 2>/dev/null || true)"
if [[ -z "$BASE_PATH" ]]; then
  BASE_PATH="/"
elif [[ "$BASE_PATH" != /* ]]; then
  BASE_PATH="/$BASE_PATH"
fi

echo "Starting HackLab at http://${HOST}:${PORT}${BASE_PATH}"
echo "Docroot: ${ROOT_DIR}"
echo "Press Ctrl+C to stop."
php "${PHP_OPTS[@]}" -S "${HOST}:${PORT}" "${ROOT_DIR}/router.php"
