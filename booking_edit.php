
<?php

require __DIR__ . '/lib/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!in_array($method, ['GET', 'POST'], true)) {
    http_response_code(405);
    exit('Method not allowed');
}

// Reference is immutable and comes from the URL.
$ref = trim((string)($_GET['ref'] ?? ''));

$bookings = $_SESSION['bookings'] ?? [];

$index = find_booking_index_by_ref($bookings, $ref);

if ($index === null) {
    http_response_code(404);
    exit(
        'Booking not found. <a href="dashboard.php">Back to Dashboard</a>'
    );
}

$booking = $bookings[$index];

$workshopId = get_booking_workshop_id($booking, $workshops);

if ($workshopId === null) {
    http_response_code(404);
    exit(
        'Booking not found. <a href="dashboard.php">Back to Dashboard</a>'
    );
}

$selectedWorkshop = $workshops[$workshopId];

$errors = [];

$old = [
    'full_name' => (string)($booking['full_name'] ?? ''),
    'email' => (string)($booking['email'] ?? ''),
    'quantity' => (string)($booking['quantity'] ?? 1)
];

// POST processing
if ($method === 'POST') {

    if (!csrf_valid($_POST)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    // Only editable fields come from POST.
    // Workshop ID is forced from server-side session data.
    $input = [
        'full_name' => $_POST['full_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'quantity' => $_POST['quantity'] ?? null,
        'workshop_id' => $workshopId
    ];

    $result = validate_booking($input, $workshops);

    $errors = $result['errors'];

    $old = [
        'full_name' => (string)($_POST['full_name'] ?? ''),
        'email' => (string)($_POST['email'] ?? ''),
        'quantity' => (string)($_POST['quantity'] ?? '')
    ];

    if (!$errors) {

        $data = $result['data'];

        // Recalculate price using trusted catalog data.
        $unitFee = (int)$selectedWorkshop['fee'];

        // Update only the matching booking.
        $_SESSION['bookings'][$index]['workshop_id'] = $workshopId;

        $_SESSION['bookings'][$index]['workshop']
            = $selectedWorkshop['title'];

        $_SESSION['bookings'][$index]['full_name']
            = $data['full_name'];

        $_SESSION['bookings'][$index]['email']
            = $data['email'];

        $_SESSION['bookings'][$index]['quantity']
            = $data['quantity'];

        $_SESSION['bookings'][$index]['amount']
            = $unitFee * $data['quantity'];

        $_SESSION['flash'] = 'Booking updated successfully.';

        redirect_to(
            'booking_detail.php?ref=' . urlencode($ref)
        );
    }

    // Validation failed: booking data remains unchanged.
}

$unitFee = (int)$selectedWorkshop['fee'];

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Booking</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="<?= e($theme) ?>">
<main class="wrap">

<header>
    <h1>Edit Booking</h1>

    <nav>
        <a href="index.php">Catalog</a>
        <a href="dashboard.php">My bookings</a>
    </nav>
</header>

<form method="post"
      action="booking_edit.php?ref=<?= urlencode($ref) ?>"
      class="panel form-stack edit-form">

    <input type="hidden"
           name="csrf_token"
           value="<?= e($_SESSION['csrf_token']) ?>">

    <label>
        Booking Reference

        <input type="text"
               value="<?= e($booking['ref']) ?>"
               readonly>
    </label>

    <label>
        Workshop

        <input type="text"
               value="<?= e($selectedWorkshop['title']) ?>"
               readonly>
    </label>

    <label>
        Full Name

        <input type="text"
               name="full_name"
               value="<?= e($old['full_name']) ?>"
               maxlength="120"
               required>
    </label>

    <small class="error">
        <?= e($errors['full_name'] ?? '') ?>
    </small>

    <label>
        Email

        <input type="email"
               name="email"
               value="<?= e($old['email']) ?>"
               required>
    </label>

    <small class="error">
        <?= e($errors['email'] ?? '') ?>
    </small>

    <label>
        Tickets (1–4)

        <input type="number"
               name="quantity"
               min="1"
               max="4"
               step="1"
               value="<?= e($old['quantity']) ?>"
               required>
    </label>

    <small class="error">
        <?= e($errors['quantity'] ?? '') ?>
    </small>

    <hr>

    <p>
        <strong>
            Current total:
            <?= number_format(
                $unitFee * (int)($old['quantity'] !== ''
                    && filter_var($old['quantity'], FILTER_VALIDATE_INT) !== false
                    ? $old['quantity']
                    : 0)
            ) ?>
            VND
        </strong>
    </p>

    <p class="muted">
        Unit fee: <?= number_format($unitFee) ?> VND
        - Total will be recalculated after saving.
    </p>

    <div class="form-actions">

        <button type="submit">
            Save Changes
        </button>

        <a class="btn secondary-btn"
           href="booking_detail.php?ref=<?= urlencode($ref) ?>">
            Cancel
        </a>

    </div>

</form>

<div class="notice warning-notice">
    The server must validate name, email and ticket quantity,
    verify the CSRF token and update the correct booking in
    $_SESSION.
</div>

</main>
</body>
</html>
