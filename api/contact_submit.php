<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('POST');

$input = get_json_input();
$name = trim((string)($input['name'] ?? ''));
$email = trim((string)($input['email'] ?? ''));
$phone = trim((string)($input['phone'] ?? ''));
$message = trim((string)($input['message'] ?? ''));

$errors = [];
if ($name === '' || mb_strlen($name) > 120) $errors[] = 'Name is required';
if ($phone === '' || mb_strlen($phone) > 40) $errors[] = 'Phone is required';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
if ($message === '' || mb_strlen($message) > 5000) $errors[] = 'Message is required';

if ($errors) {
    json_response(['success' => false, 'message' => implode(', ', $errors)], 422);
}

$stmt = db()->prepare('INSERT INTO contact_messages (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())');
$stmt->execute([$name, $email, $phone, $message]);

json_response(['success' => true, 'message' => 'Message sent successfully']);
