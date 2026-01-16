<?php

header('Content-Type: application/json');
include "db.php";

$meta_data = [];


$cloth_result = $conn->query("SELECT id, cloth_name, description FROM cloth_types WHERE is_active = TRUE ORDER BY cloth_name");
$meta_data['cloth_types'] = $cloth_result->fetch_all(MYSQLI_ASSOC);


$service_result = $conn->query("SELECT id, service_name, description, base_price FROM services WHERE is_active = TRUE ORDER BY service_name");
$meta_data['service_types'] = $service_result->fetch_all(MYSQLI_ASSOC);


$meta_data['order_statuses'] = [
    ['value' => 'Pending', 'label' => 'Pending'],
    ['value' => 'Washing', 'label' => 'Washing'],
    ['value' => 'Ironing', 'label' => 'Ironing'],
    ['value' => 'Ready', 'label' => 'Ready'],
    ['value' => 'Delivered', 'label' => 'Delivered'],
    ['value' => 'Cancelled', 'label' => 'Cancelled']
];


$meta_data['payment_modes'] = [
    ['value' => 'Cash', 'label' => 'Cash'],
    ['value' => 'Card', 'label' => 'Card'],
    ['value' => 'UPI', 'label' => 'UPI'],
    ['value' => 'Net Banking', 'label' => 'Net Banking']
];

echo json_encode(['ok' => true, 'data' => $meta_data]);
$conn->close();
?>