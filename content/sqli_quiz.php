<?php
return [
    [
        'question' => 'What does SQLi stand for?',
        'options' => ['Structured Query Language injection', 'Secure Query Login', 'Server Query Link', 'Simple Query Logic'],
        'correct' => 0,
    ],
    [
        'question' => 'Which payload is a classic auth bypass?',
        'options' => ["' OR '1'='1", 'DROP TABLE users;', '<script>alert(1)</script>', '../etc/passwd'],
        'correct' => 0,
    ],
    [
        'question' => 'What technology prevents SQLi?',
        'options' => ['Prepared statements', 'Base64 encoding', 'Client-side validation only', 'Minified JavaScript'],
        'correct' => 0,
    ],
    [
        'question' => 'True/False: Escaping alone is sufficient protection.',
        'options' => ['True', 'False'],
        'correct' => 1,
    ],
    [
        'question' => 'Which OWASP category covers SQLi?',
        'options' => ['A03:2021 – Injection', 'A07:2021 – Identification', 'A01:2021 – Access Control', 'A05:2021 – Misconfiguration'],
        'correct' => 0,
    ],
];
