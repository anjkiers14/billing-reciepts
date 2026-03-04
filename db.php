<?php
$conn = new mysqli("localhost", "root", "", "billing_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
