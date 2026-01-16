<?php

header('Content-Type: application/json');
include "db.php";

$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? '';
$phone = $input['phone'] ?? '';
$address = $input['address'] ?? '';
$email = $input['email'] ?? '';

if (empty($name)) {
    echo json_encode(['ok' => false, 'message' => 'Customer name is required']);
    exit;
}


$stmt = $conn->prepare("INSERT INTO customers (name, phone, address, email) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $phone, $address, $email);

if ($stmt->execute()) {
    echo json_encode([
        'ok' => true, 
        'message' => 'Customer added successfully',
        'customer_id' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['ok' => false, 'message' => 'Failed to add customer: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>