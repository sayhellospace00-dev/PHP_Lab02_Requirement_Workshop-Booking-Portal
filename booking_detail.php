<?php
require __DIR__ . '/lib/bootstrap.php';

$ref = trim((string)($_GET['ref'] ?? ''));

$bookings = $_SESSION['bookings'] ?? [];

$index = find_booking_index_by_ref($bookings, $ref);

$booking = $index !== null ? $bookings[$index] : null;

$workshopId = $booking !== null
    ? get_booking_workshop_id($booking, $workshops)
    : null;

$selectedWorkshop = $workshopId !== null
    ? $workshops[$workshopId]
    : null;

if ($booking === null || $selectedWorkshop === null) {
    http_response_code(404);
    $booking = null;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Booking Details</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="<?= e($theme) ?>">
<main class="wrap">

<header>
    <h1>Booking Details</h1>

    <nav>
        <a href="index.php">Catalog</a>
        <a href="dashboard.php">My bookings</a>
    </nav>
</header>

<?php if ($booking === null): ?>

    <div class="panel">
        <h2>Booking not found</h2>
        <p>The requested booking does not exist.</p>
        <a class="btn" href="dashboard.php">Back to My Bookings</a>
    </div>

<?php else: ?>

    <?php
    $quantity = (int)$booking['quantity'];
    $unitFee = (int)$selectedWorkshop['fee'];
    $total = $unitFee * $quantity;
    ?>

    <div class="panel detail-panel">

        <div class="detail-row">
            <strong>Booking Reference</strong>
            <span class="reference-tag">
                <?= e($booking['ref']) ?>
            </span>
        </div>

        <div class="detail-row">
            <strong>Attendee</strong>
            <span><?= e($booking['full_name']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Email</strong>
            <span><?= e($booking['email']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Workshop</strong>
            <span><?= e($selectedWorkshop['title']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Quantity</strong>
            <span><?= e($quantity) ?> tickets</span>
        </div>

        <div class="detail-row">
            <strong>Unit Fee</strong>
            <span><?= number_format($unitFee) ?> VND</span>
        </div>

        <div class="detail-row">
            <strong>Total Amount</strong>
            <strong><?= number_format($total) ?> VND</strong>
        </div>

        <hr>

        <a class="btn edit-btn"
           href="booking_edit.php?ref=<?= urlencode((string)$booking['ref']) ?>">
            Edit this booking
        </a>

        <a class="btn secondary-btn"
           href="dashboard.php">
            Back to My Bookings
        </a>

    </div>

    <p class="muted">
        Booking information is read from the current PHP session.
    </p>

<?php endif; ?>

</main>
</body>
</html>
