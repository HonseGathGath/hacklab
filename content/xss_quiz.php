<?php
return [
    [
        'question' => 'What does XSS stand for?',
        'options' => ['Cross-Site Scripting', 'Cross Server Session', 'XML Script Service', 'Extended SQL Script'],
        'correct' => 0,
    ],
    [
        'question' => 'Which PHP function prevents XSS output?',
        'options' => ['htmlspecialchars()', 'trim()', 'md5()', 'urldecode()'],
        'correct' => 0,
    ],
    [
        'question' => 'Stored XSS is more dangerous because...',
        'options' => ['It affects every visitor', 'It only affects the attacker', 'It only works on mobile', 'It requires admin role'],
        'correct' => 0,
    ],
    [
        'question' => 'Where does the malicious script execute?',
        'options' => ['In the victim’s browser', 'In the database', 'In the web server kernel', 'In the firewall'],
        'correct' => 0,
    ],
    [
        'question' => 'True/False: Using a WAF alone is sufficient to prevent XSS.',
        'options' => ['True', 'False'],
        'correct' => 1,
    ],
];
