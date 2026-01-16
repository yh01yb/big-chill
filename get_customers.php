<?php

header('Content-Type: application/json');
include "db.php";

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $conn->prepare("SELECT id, name, phone, address, email, created_at 
                           FROM customers 
                           WHERE name LIKE ? OR phone LIKE ? OR email LIKE ?
                           ORDER BY name");
    $search_term = "%$search%";
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
} else {
    $stmt = $conn->prepare("SELECT id, name, phone, address, email, created_at 
                           FROM customers 
                           ORDER BY name");
}

$stmt->execute();
$result = $stmt->get_result();
$customers = [];

while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

echo json_encode(['ok' => true, 'customers' => $customers]);
$stmt->close();
$conn->close();
?>