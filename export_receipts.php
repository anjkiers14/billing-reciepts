<?php
include "db.php";

// Set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=receipts.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output column headings
fputcsv($output, array('ID', 'Name', 'Receipt #', 'Date', 'Subtotal', 'Tax', 'Total'));

// Fetch receipts from database
$rows = $conn->query("SELECT * FROM receipts ORDER BY id DESC");

if($rows->num_rows > 0){
    while($row = $rows->fetch_assoc()){
        fputcsv($output, array(
            $row['id'],
            $row['store_name'],
            $row['receipt_number'],
            $row['receipt_date'],
            $row['subtotal'],
            $row['tax'],
            $row['total']
        ));
    }
}
fclose($output);
exit();
?>
