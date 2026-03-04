<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "db.php";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $customer_id = intval($_GET['id']);

    // Get previous reading
    $reading = $conn->query("
        SELECT current_reading 
        FROM receipts 
        WHERE id = $customer_id 
        ORDER BY id DESC 
        LIMIT 1
    ");

    if (!$reading) {
        die("Error in reading query: " . $conn->error);
    }

    $previous_reading = 0;
    if ($reading->num_rows > 0) {
        $row = $reading->fetch_assoc();
        $previous_reading = $row['current_reading'];
    }

    // If you don't have address column, remove this query
    echo json_encode([
        "previous_reading" => $previous_reading,
        "address" => "" // remove address until you create customers table
    ]);
}
?>