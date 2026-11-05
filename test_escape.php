<?php

require __DIR__ . '/lib/functions.php';

$inputs = [
    '<b>Mai</b>',
    'Tom & Jerry',
    '"Hello World"',
    "'Hello World'",
    '<script>alert("test")</script>'
];

foreach ($inputs as $input) {

    echo '<p>' . e($input) . '</p>';

}