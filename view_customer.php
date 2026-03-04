<?php
include "db.php";

if (!isset($_GET['store_name'])) {
    die("Customer not specified.");
}

$store_name = $conn->real_escape_string($_GET['store_name']);

$receipts = $conn->query("
    SELECT * FROM receipts 
    WHERE store_name='$store_name' 
    ORDER BY receipt_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Receipts for <?php echo htmlspecialchars($store_name); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* General Styles */
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
    text-align: center;
}
.container { 
    max-width: 1000px; 
    margin: 20px auto; 
    padding: 20px; 
}
.buttons { 
    margin-bottom: 15px; 
}
.btn {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    font-size: 14px;
    margin-right: 5px;
    margin-top: 10px;
}
.back { background: #6c757d; }
.excel { background: #28a745; }
.btn:hover { opacity: 0.85; }

table { 
    width: 100%; 
    border-collapse: collapse; 
    background: white; 
    margin-top: 15px;
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

.action-btn {
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    color: white;
}
.view-btn { background: #007bff; }
.edit-btn { background: #ffc107; }

/* ---------- Responsive Styles ---------- */

/* Mobile & Tablet Styles */
@media (max-width: 768px) {
    body {
        padding: 20px;
    }
    
    .container {
        padding: 15px;
    }

    h2 {
        font-size: 18px;
    }

    .buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .btn {
        width: 100%;
        margin-bottom: 10px;
        text-align: center;
    }

    table {
        width: 100%;
        display: block;
        overflow-x: auto;
        margin-top: 20px;
    }

    th, td {
        font-size: 12px;
        padding: 8px;
    }

    .action-btn {
        display: block;
        font-size: 12px;
        width: 100%;
        text-align: center;
        margin-bottom: 5px;
    }

    .view-btn, .edit-btn {
        width: 100%;
    }
}

/* Larger Screen (Desktops) */
@media (min-width: 769px) {
    table {
        width: 100%;
        table-layout: auto;
    }

    th, td {
        font-size: 14px;
        padding: 10px;
    }
}
</style>
</head>

<body>

<div class="header">
    Receipts for <?php echo htmlspecialchars($store_name); ?>
</div>

<div class="container">

    <div class="buttons">
        <a href="view_receipts.php" class="btn back">← Back</a>
        <a href="export_receipts2.php?store_name=<?php echo urlencode($store_name); ?>" 
           class="btn excel">
           ⬇ Download Excel
        </a>
    </div>

    <?php if ($receipts->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Receipt #</th>
                    <th>Date</th>
                    <th>Previous</th>
                    <th>Current</th>
                    <th>Consumption</th>
                    <th>Total (PHP)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $receipts->fetch_assoc()): 
                $consumption = $row['current_reading'] - $row['previous_reading'];
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['receipt_number']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['receipt_date'])); ?></td>
                    <td><?php echo number_format($row['previous_reading'], 2); ?></td>
                    <td><?php echo number_format($row['current_reading'], 2); ?></td>
                    <td><?php echo number_format($consumption, 2); ?></td>
                    <td><?php echo number_format($row['total'], 2); ?></td>
                    <td>
                        <a class="action-btn view-btn" href="view_receipts.php?id=<?php echo $row['id']; ?>">View</a>
                        <a class="action-btn edit-btn" href="edit_receipts.php?id=<?php echo $row['id']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color:#777;">No receipts found.</p>
    <?php endif; ?>

</div>

</body>
</html>