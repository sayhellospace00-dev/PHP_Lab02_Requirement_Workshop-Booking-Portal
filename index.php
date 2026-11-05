<?php
$workshops = require __DIR__ . '/data/workshops.php';
foreach ($workshops as $item) {
    echo '<p>' . htmlspecialchars($item['title']) . '</p>';
}
