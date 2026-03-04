<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$customer = $_POST['customer_name'];
$account = $_POST['account_number'];
$billing_date = $_POST['billing_date'];

$previous = floatval($_POST['previous_reading']);
$current = floatval($_POST['current_reading']);
$consumption = floatval($_POST['consumption']);
$rate = floatval($_POST['rate_per_cubic']);
$amount_due = floatval($_POST['amount_due']);

$subtotal = $consumption * $rate;
$tax = 0;
$total = $amount_due;

/* ✅ INSERT WITHOUT receipt_number FIRST */
$stmt = $conn->prepare("
    INSERT INTO receipts 
    (store_name, receipt_date, 
     previous_reading, current_reading, consumption, rate_per_cubic,
     subtotal, tax, total) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssddddddd",
    $customer,
    $billing_date,
    $previous,
    $current,
    $consumption,
    $rate,
    $subtotal,
    $tax,
    $total
);

$stmt->execute();

/* ✅ GET INSERTED ID */
$receipt_id = $stmt->insert_id;

/* ✅ GENERATE RECEIPT NUMBER */
$receipt_number = "BACIWA-" . date("Y") . "-" . str_pad($receipt_id, 5, "0", STR_PAD_LEFT);

/* ✅ UPDATE receipt_number COLUMN */
$update = $conn->prepare("UPDATE receipts SET receipt_number=? WHERE id=?");
$update->bind_param("si", $receipt_number, $receipt_id);
$update->execute();

$stmt->close();
$conn->close();

/* Redirect */
header("Location: view_receipts.php?id=" . $receipt_id);
exit();
?>