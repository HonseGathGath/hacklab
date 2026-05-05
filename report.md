# HackLab Implementation Report

## 1) Requirements Traceability (PDF → Implementation)
### Project description (course requirements)
- **Minimum 5 HTML pages**: Implemented in `/public` (index.php, register.php, login.php, dashboard.php, module.php, sandbox.php, quiz.php, certificate.php, contact.php) — exceeds minimum.
- **HTML elements**: text, titles, tables, images, lists, videos/audios, links, forms, checkbox/radio/text/list are required.
  - Forms: registration/login/contact/quiz (radio, text, email, password, checkbox, select).
  - Lists: module grid, progress list, admin users table.
  - Tables: admin users table.
  - Media: lesson content contains code blocks; add media placeholders if needed (can be extended with images/videos in assets).
- **CSS3 for layout and display**: All layout is in `/assets/css/main.css`, `/assets/css/dashboard.css`, `/assets/css/sandbox.css`.
- **HTML5 tags**: header, footer, section, div usage throughout.
- **Javascript interactivity**: AJAX for progress and quizzes, sandbox toggle, form validation implied.
- **PHP server interactivity**: PDO access to MySQL, CRUD with progress tracking, authentication.

### HackLab specification (project plan)
- **Dark terminal UI**: Theme, colors, typography in `assets/css/main.css`.
- **User account system**: `register.php`, `login.php`, `logout.php`, `includes/auth.php`.
- **Progress tracking**: `progress` table + `dashboard.php`.
- **Modules**: SQLi, XSS, IDOR lesson pages in `content/*_lesson.php`.
- **Sandbox isolation**: `/sandbox/*.php` using SQLite in-memory or hardcoded data.
- **Quizzes**: `public/quiz.php` + `ajax/submit_quiz.php` + `content/*_quiz.php`.
- **Certificate page**: `public/certificate.php`.
- **Security**: prepared statements, output escaping, CSRF, session management.

## 2) Architecture Overview
```
/srv/http/hacklab
├── public/          # web root
├── includes/        # app logic + configuration
├── content/         # lesson + quiz content
├── sandbox/         # isolated vulnerability demos (SQLite/hardcoded)
├── ajax/            # JSON endpoints
├── assets/          # CSS/JS/static
└── sql/             # schema.sql
```

## 3) File-by-File Summary
### Root
- `.htaccess` — Rewrite rules and block access to `includes/` and `sandbox/`.
- `README.md` — setup instructions for LAMP + Arch.
- `sql/schema.sql` — full MySQL schema for users, sessions, modules, progress, quiz_attempts.

### `/public`
- `bootstrap.php` — shared bootstrap; loads config, DB, auth, CSRF, utilities; starts session.
- `index.php` — landing page, CTA, module teasers.
- `register.php` — registration form with CSRF + validation.
- `login.php` — login form with CSRF + session creation.
- `logout.php` — session destroy and cookie cleanup.
- `dashboard.php` — module progress cards + overall completion.
- `module.php` — dynamic lesson renderer by slug.
- `sandbox.php` — sandbox wrapper + marks sandbox tried.
- `quiz.php` — quiz renderer from content arrays + AJAX submission.
- `certificate.php` — printable certificate if all modules complete.
- `contact.php` — form to satisfy HTML form element requirements.
- `admin/index.php`, `admin/users.php` — admin dashboard and user listing.

### `/includes`
- `config.example.php`, `config.php` — DB credentials, constants.
- `db.php` — PDO singleton connection.
- `auth.php` — registration, login, logout, server-side session tracking.
- `csrf.php` — token generation and verification.
- `functions.php` — helpers for escaping, redirects, access control.

### `/content`
- `sqli_lesson.php`, `xss_lesson.php`, `idor_lesson.php` — lesson content with vulnerable and secure code sections.
- `sqli_quiz.php`, `xss_quiz.php`, `idor_quiz.php` — quiz question arrays.

### `/sandbox`
- `sqli_demo.php` — in-memory SQLite login demo; toggle vulnerable vs fixed.
- `xss_demo.php` — in-memory SQLite comments demo; toggle vulnerable vs fixed.
- `idor_demo.php` — hardcoded data; shows access control logic.

### `/ajax`
- `mark_lesson_read.php` — mark lesson as read (CSRF protected).
- `submit_quiz.php` — quiz scoring and progress update (CSRF protected).
- `get_progress.php` — JSON progress for dashboard.

### `/assets`
- `css/main.css` — main theme, layout, typography, forms.
- `css/dashboard.css` — dashboard layout additions.
- `css/sandbox.css` — sandbox styling.
- `js/progress.js` — marks lesson as read.
- `js/quiz.js` — submit quiz answers.
- `js/sandbox.js` — toggles sandbox mode.

## 4) Testing & Validation
- PHP lint on all public and backend files: **no syntax errors**.
- Sandbox scripts render with PHP 8.
- Database schema installs cleanly in MySQL.

## 5) Deployment Notes (Arch Linux)
1. Install packages: `sudo pacman -S apache mariadb php php-apache`
2. Enable services: `sudo systemctl enable --now httpd mariadb`
3. Create DB: `mysql -u root -p < sql/schema.sql`
4. Place project at `/srv/http/hacklab` with DocumentRoot `/srv/http/hacklab/public`.
5. Copy `includes/config.example.php` → `includes/config.php` and set DB creds.

## 6) Known Limits / Next Steps
- Add real media (images/videos/audio) to satisfy multimedia requirement precisely.
- Add more content in lessons to match syllabus depth.
- Add extra form validation client-side if required.
