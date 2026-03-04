<?php
include "db.php";

if (!isset($_GET['store_name'])) {
    echo json_encode(['receipt_number' => '']);
    exit;
}

$customer_name = $_GET['store_name'];

// Fetch account number safely
$stmt = $conn->prepare("SELECT receipt_number FROM receipts WHERE store_name=? LIMIT 1");
$stmt->bind_param("s", $store_name);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->num_rows > 0 ? $result->fetch_assoc() : ['receipt_number' => ''];
$stmt->close();

echo json_encode($data);
?>