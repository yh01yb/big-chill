<?php

header('Content-Type: application/json');
include "db.php";

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? 0;
$status = $input['status'] ?? '';

if (empty($order_id) || empty($status)) {
    echo json_encode(['ok' => false, 'message' => 'Order ID and status are required']);
    exit;
}

$valid_statuses = ['Pending', 'Washing', 'Ironing', 'Ready', 'Delivered', 'Cancelled'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['ok' => false, 'message' => 'Invalid status']);
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'message' => 'Order status updated successfully']);
} else {
    echo json_encode(['ok' => false, 'message' => 'Failed to update order status: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>