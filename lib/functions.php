<?php
declare(strict_types=1);

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}


function normalize_text(string $text): string {
    $text = strip_tags(trim($text));
    return preg_replace('/\s+/u', ' ', $text) ?? $text;
}

function validate_booking(array $input): array {
    $errors = [];
    $name = normalize_text((string)($input['full_name'] ?? ''));
    $email = trim((string)($input['email'] ?? ''));
    $qty = filter_var(
        $input['quantity'] ?? null,
        FILTER_VALIDATE_INT
    );

    // Validate name
    if ($name === '') {
        $errors['full_name'] = 'Full name is required.';
    } elseif (strlen($name) < 2 || strlen($name) > 120) {
        $errors['full_name'] = 'Name must have 2–120 bytes.';
    }

    // Validate email
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Invalid email format.';
    }

    // Validate quantity
    if ($qty === false || $qty < 1 || $qty > 4) {
        $errors['quantity'] = 'Quantity must be 1 to 4.';
    }

    return ['errors' => $errors, 'data' => [
            'full_name' => $name, 'email' => $email,
            'quantity' => $qty === false ? 0 : $qty
        ]];
}