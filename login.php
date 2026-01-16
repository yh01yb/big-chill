<?php

header('Content-Type: application/json');
include "db.php";

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['ok' => false, 'message' => 'Username and password are required']);
    exit;
}


$stmt = $conn->prepare("SELECT id, username, email, full_name, phone FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'ok' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'phone' => $user['phone']
        ]
    ]);
} else {
    echo json_encode(['ok' => false, 'message' => 'Invalid username or password']);
}

$stmt->close();
$conn->close();
?>