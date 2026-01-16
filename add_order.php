<?php

header('Content-Type: application/json');
include "db.php";

$input = json_decode(file_get_contents('php://input'), true);
$customer_id = $input['customer_id'] ?? 0;
$items = $input['items'] ?? [];
$special_instructions = $input['special_instructions'] ?? '';


if (empty($customer_id) || !is_array($items) || count($items) === 0) {
    echo json_encode(['ok' => false, 'message' => 'Customer and at least one item are required']);
    exit;
}

$conn->begin_transaction();

try {
    
    $total_amount = 0;
    foreach ($items as $item) {
        $quantity = intval($item['quantity'] ?? 1);
        $price = floatval($item['price'] ?? 0);
        $total_amount += ($quantity * $price);
    }

    
    $order_stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, special_instructions) VALUES (?, ?, ?)");
    $order_stmt->bind_param("ids", $customer_id, $total_amount, $special_instructions);
    
    if (!$order_stmt->execute()) {
        throw new Exception("Failed to create order: " . $order_stmt->error);
    }
    
    $order_id = $order_stmt->insert_id;
    $order_stmt->close();

    
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, cloth_type, service_type, quantity, price, notes) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $cloth_type = $item['cloth_type'] ?? '';
        $service_type = $item['service_type'] ?? '';
        $quantity = intval($item['quantity'] ?? 1);
        $price = floatval($item['price'] ?? 0);
        $notes = $item['notes'] ?? '';
        
        $item_stmt->bind_param("issids", $order_id, $cloth_type, $service_type, $quantity, $price, $notes);
        
        if (!$item_stmt->execute()) {
            throw new Exception("Failed to add order item: " . $item_stmt->error);
        }
    }
    $item_stmt->close();

    
    $payment_stmt = $conn->prepare("INSERT INTO payments (order_id, amount, status) VALUES (?, ?, 'Pending')");
    $payment_stmt->bind_param("id", $order_id, $total_amount);
    
    if (!$payment_stmt->execute()) {
        throw new Exception("Failed to create payment record: " . $payment_stmt->error);
    }
    $payment_stmt->close();

    $conn->commit();
    
    echo json_encode([
        'ok' => true,
        'message' => 'Order created successfully',
        'order_id' => $order_id,
        'total_amount' => $total_amount
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>