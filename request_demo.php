<?php
require __DIR__ . '/lib/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$getName = (string)($_GET['name'] ?? '');
$postName = (string)($_POST['name'] ?? '');
$requestName = (string)($_REQUEST['name'] ?? '');
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Request Inspector</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= e($theme) ?>">
<main class="wrap">
    <h1>Request Inspector</h1>

    <p>Method: <?= htmlspecialchars($method) ?></p>

    <p>GET name: <?= htmlspecialchars($getName) ?></p>

    <p>POST name: <?= htmlspecialchars($postName) ?></p>

    <p>REQUEST name: <?= htmlspecialchars($requestName) ?></p>

    <form method="get">
        <input name="name" placeholder="GET name">
        <button>Send GET</button>
    </form>

    <form method="post">
        <input name="name" placeholder="POST name">
        <button>Send POST</button>
    </form>

</body>
</html>