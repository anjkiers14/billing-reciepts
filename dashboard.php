<?php include "db.php"; ?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard - Receipt System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}

.header {
    background: #007bff;
    color: white;
    padding: 15px 30px;
    font-size: 22px;
    font-weight: 600;
}

.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
}

.back-btn {
    display: inline-block;
    background: #6c757d;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    margin-bottom: 15px;
}

.back-btn:hover {
    opacity: 0.8;
}

.cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.card {
    flex: 1;
    min-width: 220px;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    text-align: center;
}

.card h3 {
    margin: 0 0 10px 0;
    color: #555;
}

.card p {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
}

.card a {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #007bff;
    font-size: 14px;
}

.card a:hover {
    text-decoration: underline;
}

.recent-receipts {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.recent-receipts h3 {
    margin-top: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background: #007bff;
    color: white;
}

tr:hover {
    background: #f2f2f2;
}

.view-btn {
    background: #007bff;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 13px;
}

.view-btn:hover {
    opacity: 0.8;
}

.export-btn {
    display:inline-block;
    margin-bottom:10px;
    padding:8px 12px;
    background:#28a745;
    color:white;
    border-radius:5px;
    text-decoration:none;
    font-size:14px;
}

.export-btn:hover {
    opacity:0.9;
}
</style>
</head>
<body>

<div class="header">
    Receipt / Water Billing Dashboard
</div>

<div class="container">

<a href="index.php" class="back-btn">← Back</a>

<?php
$totalReceipts = $conn->query("SELECT COUNT(*) as total FROM receipts")->fetch_assoc()['total'];
$totalIncome = $conn->query("SELECT SUM(total) as income FROM receipts")->fetch_assoc()['income'];
$recentReceipts = $conn->query("SELECT * FROM receipts ORDER BY id DESC LIMIT 5");
?>

<div class="cards">
    <div class="card">
        <h3>Total Receipts</h3>
        <p><?php echo $totalReceipts ?: 0; ?></p>
        <a href="view_receipts.php">View All</a>
    </div>

    <div class="card">
        <h3>Total Amount Collected</h3>
        <p>PHP <?php echo number_format($totalIncome ?: 0, 2); ?></p>
        <a href="view_receipts.php">View All</a>
    </div>

    <div class="card">
        <h3>Create New Receipt</h3>
        <p>&nbsp;</p>
        <a href="index.php">Create Now</a>
    </div>
</div>

<div class="recent-receipts">
    <h3>Recent Receipts</h3>

    <a href="export_receipts.php" class="export-btn">Download Excel</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Receipt #</th>
            <th>Date</th>
            <th>Total (PHP)</th>
            <th>Action</th>
        </tr>

        <?php
        if ($recentReceipts->num_rows > 0) {
            while ($row = $recentReceipts->fetch_assoc()) {
                echo "<tr>
                        <td>".htmlspecialchars($row['id'])."</td>
                        <td>.".htmlspecialchars($row['store_name'])."</td>
                        <td>".htmlspecialchars($row['receipt_number'])."</td>
                        <td>".date('M d, Y', strtotime($row['receipt_date']))."</td>
                        <td>".number_format($row['total'], 2)."</td>
                        <td><a class='view-btn' href='view_customer.php?store_name=".$row['store_name']."'>View</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;color:#777;'>No receipts found.</td></tr>";
        }
        ?>
    </table>
</div>

</div>

</body>
</html>
