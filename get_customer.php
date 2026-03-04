<?php
include "db.php";

if(isset($_POST['customer_id'])){

    $customer_id = intval($_POST['customer_id']);

    // Get latest reading
    $reading = $conn->query("
        SELECT current_reading 
        FROM receipts 
        WHERE customer_id = $customer_id 
        ORDER BY id DESC 
        LIMIT 1
    ");

    $previous_reading = 0;

    if($reading->num_rows > 0){
        $row = $reading->fetch_assoc();
        $previous_reading = $row['current_reading'];
    }

    // Get customer address
    $customer = $conn->query("
        SELECT address 
        FROM customers 
        WHERE id = $customer_id
    ");

    $cust = $customer->fetch_assoc();

    echo json_encode([
        "previous_reading" => $previous_reading,
        "address" => $cust['address']
    ]);
}
?>