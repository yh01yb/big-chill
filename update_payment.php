<?php

header('Content-Type: application/json');
include "db.php";

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? 0;
$payment_mode = $input['payment_mode'] ?? 'Cash';
$status = $input['status'] ?? 'Completed';
$transaction_id = $input['transaction_id'] ?? '';

if (empty($order_id)) {
    echo json_encode(['ok' => false, 'message' => 'Order ID is required']);
    exit;
}

$stmt = $conn->prepare("UPDATE payments SET payment_mode = ?, status = ?, transaction_id = ?, payment_date = NOW() WHERE order_id = ?");
$stmt->bind_param("sssi", $payment_mode, $status, $transaction_id, $order_id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'message' => 'Payment updated successfully']);
} else {
    echo json_encode(['ok' => false, 'message' => 'Failed to update payment: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>