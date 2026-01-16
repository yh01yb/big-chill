<?php

header('Content-Type: application/json');
include "db.php";

$stats = [];


$result = $conn->query("SELECT COUNT(*) as total FROM customers");
$stats['total_customers'] = $result->fetch_assoc()['total'];


$result = $conn->query("SELECT COUNT(*) as total FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['total'];


$result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE DATE(order_date) = CURDATE()");
$stats['today_orders'] = $result->fetch_assoc()['total'];


$result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status IN ('Pending','Washing','Ironing','Ready')");
$stats['pending_orders'] = $result->fetch_assoc()['total'];


$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE status != 'Cancelled'");
$stats['total_revenue'] = $result->fetch_assoc()['revenue'];


$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE DATE(order_date) = CURDATE() AND status != 'Cancelled'");
$stats['today_revenue'] = $result->fetch_assoc()['revenue'];


$result = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$stats['status_distribution'] = [];
while ($row = $result->fetch_assoc()) {
    $stats['status_distribution'][$row['status']] = $row['count'];
}

echo json_encode(['ok' => true, 'stats' => $stats]);
$conn->close();
?>