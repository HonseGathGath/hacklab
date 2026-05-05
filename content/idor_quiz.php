<?php
return [
    [
        'question' => 'What does IDOR stand for?',
        'options' => ['Insecure Direct Object Reference', 'Internal Data Object Reset', 'Indexed Data Output Route', 'Identity Drop-Off Risk'],
        'correct' => 0,
    ],
    [
        'question' => 'Which HTTP parameter type is most commonly exploited in IDOR?',
        'options' => ['Numeric ID in URL', 'Cookie name', 'User-Agent header', 'HTTPS port'],
        'correct' => 0,
    ],
    [
        'question' => 'The fix for IDOR is best described as...',
        'options' => ['Server-side authorization checks', 'Switching to POST', 'Client-side hiding of IDs', 'Obfuscating HTML'],
        'correct' => 0,
    ],
    [
        'question' => 'True/False: Using POST instead of GET prevents IDOR.',
        'options' => ['True', 'False'],
        'correct' => 1,
    ],
    [
        'question' => 'Which OWASP 2021 category covers IDOR?',
        'options' => ['A01:2021 – Broken Access Control', 'A03:2021 – Injection', 'A05:2021 – Misconfiguration', 'A07:2021 – Identification'],
        'correct' => 0,
    ],
];
