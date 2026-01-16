<?php

header('Content-Type: application/json');
include "db.php";

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';
$email = $input['email'] ?? '';
$full_name = $input['full_name'] ?? '';
$phone = $input['phone'] ?? '';


if (empty($username) || empty($password) || empty($email) || empty($full_name)) {
    echo json_encode(['ok' => false, 'message' => 'All fields are required except phone']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'message' => 'Invalid email format']);
    exit;
}


$check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check_stmt->bind_param("ss", $username, $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo json_encode(['ok' => false, 'message' => 'Username or email already exists']);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

$stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $password, $email, $full_name, $phone);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'message' => 'Account created successfully! You can now login.']);
} else {
    echo json_encode(['ok' => false, 'message' => 'Registration failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>