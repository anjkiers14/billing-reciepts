<?php
include "db.php";

// Check if specific customer is requested
$store_name = isset($_GET['store_name']) 
    ? $conn->real_escape_string($_GET['store_name']) 
    : null;

// Set filename
if ($store_name) {
    $filename = str_replace(" ", "_", $store_name) . "_receipts.csv";
} else {
    $filename = "all_receipts.csv";
}

// Force download
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=$filename");

// Open output stream
$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, array(
    'ID',
    'Customer Name',
    'Receipt #',
    'Date',
    'Previous Reading',
    'Consumption',
    'Total'
));

// Build query
if ($store_name) {
    $query = "SELECT * FROM receipts 
              WHERE store_name='$store_name' 
              ORDER BY receipt_date DESC";
} else {
    $query = "SELECT * FROM receipts 
              ORDER BY receipt_date DESC";
}

$rows = $conn->query($query);

if ($rows->num_rows > 0) {
    while ($row = $rows->fetch_assoc()) {

        $consumption = $row['current_reading'] - $row['previous_reading'];

        fputcsv($output, array(
            $row['id'],
            $row['store_name'],
            $row['receipt_number'],
            $row['receipt_date'],
            $row['previous_reading'],
            $consumption,
            $row['total']
        ));
    }
}

fclose($output);
exit();
?>
