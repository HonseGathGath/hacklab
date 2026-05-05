<?php
declare(strict_types=1);

require_once __DIR__ . '/../public/bootstrap.php';
require_login();

$payload = json_decode(file_get_contents('php://input'), true);
$module = $payload['module'] ?? '';
$answers = $payload['answers'] ?? [];

if (empty($payload['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $payload['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$quizMap = [
    'sqli' => require __DIR__ . '/../content/sqli_quiz.php',
    'xss' => require __DIR__ . '/../content/xss_quiz.php',
    'idor' => require __DIR__ . '/../content/idor_quiz.php',
];
if (!isset($quizMap[$module])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid module']);
    exit;
}

$questions = $quizMap[$module];
$score = 0;
foreach ($questions as $index => $question) {
    $selected = $answers[$index] ?? null;
    if ($selected !== null && (int) $selected === (int) $question['correct']) {
        $score++;
    }
}

$stmt = db()->prepare('SELECT id FROM modules WHERE slug = :slug');
$stmt->execute([':slug' => $module]);
$moduleRow = $stmt->fetch();
if (!$moduleRow) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid module']);
    exit;
}

$passed = $score >= 4 ? 1 : 0;
$stmt = db()->prepare('INSERT INTO quiz_attempts (user_id, module_id, score, max_score, passed) VALUES (:user_id, :module_id, :score, :max, :passed)');
$stmt->execute([
    ':user_id' => $_SESSION['user_id'],
    ':module_id' => $moduleRow['id'],
    ':score' => $score,
    ':max' => count($questions),
    ':passed' => $passed,
]);

$query = 'INSERT INTO progress (user_id, module_id, quiz_passed) VALUES (:user_id, :module_id, :quiz_passed)';
if (db_runtime_driver() === 'sqlite') {
    $query .= ' ON CONFLICT(user_id, module_id) DO UPDATE SET quiz_passed = excluded.quiz_passed';
} else {
    $query .= ' ON DUPLICATE KEY UPDATE quiz_passed = VALUES(quiz_passed)';
}
$stmt = db()->prepare($query);
$stmt->execute([
    ':user_id' => $_SESSION['user_id'],
    ':module_id' => $moduleRow['id'],
    ':quiz_passed' => $passed,
]);

echo json_encode([
    'score' => $score,
    'max' => count($questions),
    'passed' => $passed,
]);
