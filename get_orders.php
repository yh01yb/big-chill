<?php

header('Content-Type: application/json');
include "db.php";

$status = $_GET['status'] ?? '';
$customer_id = $_GET['customer_id'] ?? '';


$sql = "SELECT o.id, o.customer_id, o.total_amount, o.status, o.order_date, o.special_instructions,
               c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
               p.status as payment_status, p.payment_mode
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        LEFT JOIN payments p ON p.order_id = o.id
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($status) && $status !== 'all') {
    $sql .= " AND o.status = ?";
    $params[] = $status;
    $types .= "s";
}

if (!empty($customer_id)) {
    $sql .= " AND o.customer_id = ?";
    $params[] = $customer_id;
    $types .= "i";
}

$sql .= " ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = [];

while ($order = $result->fetch_assoc()) {
    $order_id = $order['id'];
    
   
    $items_sql = "SELECT cloth_type, service_type, quantity, price, notes 
                  FROM order_items 
                  WHERE order_id = ?";
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }
    $items_stmt->close();
    
    $order['items'] = $items;
    $orders[] = $order;
}

echo json_encode(['ok' => true, 'orders' => $orders]);
$stmt->close();
$conn->close();
?>