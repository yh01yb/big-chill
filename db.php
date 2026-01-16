<?php

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'dbg_laundry';


$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);


if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset("utf8mb4");
?>