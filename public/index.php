<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Learn the attack. Build the defence.</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="page">
    <header class="hero">
        <div class="hero__nav">
            <div class="logo">HackLab</div>
            <nav>
                <?php if (is_logged_in()): ?>
                    <a class="button" href="dashboard.php">Dashboard</a>
                    <a class="button button--ghost" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="button" href="register.php">Register</a>
                    <a class="button button--ghost" href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
        <div class="hero__content">
            <p class="hero__tag">Learn the attack. Build the defence.</p>
            <h1>HackLab — Cybersecurity training for the real world.</h1>
            <p class="hero__subtitle">A dark-themed, CTF-inspired learning platform that explains SQLi, XSS, and IDOR with safe sandboxes, quizzes, and progress tracking.</p>
            <div class="hero__cta">
                <a class="button" href="register.php">Join the dojo</a>
                <a class="button button--ghost" href="login.php">I already have an account</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="module-grid">
            <article class="card">
                <h3>SQL Injection</h3>
                <p>Break the query. Own the database. Learn how prepared statements stop it.</p>
                <span class="badge">OWASP A03:2021</span>
            </article>
            <article class="card">
                <h3>Stored XSS</h3>
                <p>Watch scripts execute in the browser and learn how to escape safely.</p>
                <span class="badge">OWASP A03:2021</span>
            </article>
            <article class="card">
                <h3>IDOR</h3>
                <p>See how exposed IDs leak data and how to enforce authorization checks.</p>
                <span class="badge">OWASP A01:2021</span>
            </article>
        </section>
        <section class="card" style="margin-top: 24px;">
            <h3>Contact the team</h3>
            <p>Questions about modules or certification? Reach out to the HackLab staff.</p>
            <a class="button" href="contact.php">Open contact form</a>
        </section>
    </main>
</body>
</html>
