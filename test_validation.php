<?php

require __DIR__ . '/lib/functions.php';

$testCases = [
    [
        'full_name' => '',
        'email' => 'abc',
        'quantity' => '0'
    ],
    [
        'full_name' => 'Lan',
        'email' => 'lan@example.com',
        'quantity' => '1.5'
    ],
    [
        'full_name' => 'Minh',
        'email' => 'minh@example.com',
        'quantity' => '-1'
    ],
    [
        'full_name' => 'An',
        'email' => 'an@example.com',
        'quantity' => '5'
    ],
    [
        'full_name' => 'Nguyen Van Minh',
        'email' => 'minh@example.com',
        'quantity' => '2'
    ]
];

foreach ($testCases as $index => $input) {
    $result = validate_booking($input);

    echo "Test " . ($index + 1) . ": ";

    echo empty($result['errors']) ? "PASS" : "REJECTED";

    echo PHP_EOL;

    print_r($result['errors']);
}