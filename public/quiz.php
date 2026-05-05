<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_login();

$module = $_GET['module'] ?? '';
$questionsMap = [
    'sqli' => require __DIR__ . '/../content/sqli_quiz.php',
    'xss' => require __DIR__ . '/../content/xss_quiz.php',
    'idor' => require __DIR__ . '/../content/idor_quiz.php',
];
if (!isset($questionsMap[$module])) {
    http_response_code(404);
    echo 'Quiz not found.';
    exit;
}

$questions = $questionsMap[$module];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz — HackLab</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="page">
    <header class="module-header">
        <a href="module.php?slug=<?= e($module) ?>" class="button button--ghost">← Lesson</a>
        <h1>Quiz: <?= strtoupper(e($module)) ?></h1>
    </header>
    <main class="quiz">
        <form id="quiz-form" data-module="<?= e($module) ?>">
            <?= csrf_field(); ?>
            <?php foreach ($questions as $index => $question): ?>
                <div class="card quiz__question">
                    <h3><?= e($question['question']) ?></h3>
                    <?php foreach ($question['options'] as $optionIndex => $option): ?>
                        <label class="radio">
                            <input type="radio" name="q<?= $index ?>" value="<?= $optionIndex ?>" required>
                            <?= e($option) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <button class="button" type="submit">Submit quiz</button>
        </form>
        <div id="quiz-result" class="quiz__result"></div>
    </main>
    <script src="../assets/js/quiz.js"></script>
</body>
</html>
