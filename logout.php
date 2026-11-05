<?php
require __DIR__ . '/lib/bootstrap.php';

$_SESSION = [];
session_destroy();
header('Location: index.php', true, 303);
exit;

