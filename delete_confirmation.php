<?php
include "db.php";

if (!isset($_GET['id'])) {
    header("Location: view_receipts.php");
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM receipts WHERE id = $id");

if ($result->num_rows == 0) {
    echo "Receipt not found.";
    exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Confirm Delete</title>
<style>
body { font-family: Arial; background:#f4f6f9; padding:40px; }
.box { max-width:500px; margin:auto; background:white; padding:30px;
       border-radius:8px; box-shadow:0 5px 20px rgba(0,0,0,0.1); text-align:center; }
h2 { margin-bottom:20px; }
.btn { padding:8px 15px; border-radius:5px; text-decoration:none; font-weight:bold; margin:5px; }
.confirm { background:#dc3545; color:white; }
.cancel { background:#6c757d; color:white; }
</style>
</head>
<body>

<div class="box">
    <h2>Delete Receipt?</h2>
    <p>
        Are you sure you want to delete receipt 
        <strong>#<?= htmlspecialchars($row['receipt_number']) ?></strong>
        for <strong><?= htmlspecialchars($row['store_name']) ?></strong>?
    </p>

    <br>

    <a class="btn confirm" href="delete_receipts.php?id=<?= $id ?>">Yes, Delete</a>
    <a class="btn cancel" href="view_receipts.php">Cancel</a>
</div>

</body>
</html>