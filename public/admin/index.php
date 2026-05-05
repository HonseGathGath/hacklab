<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — HackLab</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
</head>
<body class="page">
    <main class="admin">
        <h1>Admin Dashboard</h1>
        <ul>
            <li><a href="users.php">View registered users</a></li>
        </ul>
    </main>
</body>
</html>
