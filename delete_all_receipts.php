<?php
include "db.php";

// Delete all receipts
$conn->query("DELETE FROM receipts");

// Reset auto-increment to 1 so new entries start at ID 1
$conn->query("ALTER TABLE receipts AUTO_INCREMENT = 1");

// Redirect back to all_receipts.php
header("Location: view_receipts.php");
exit();
?>
