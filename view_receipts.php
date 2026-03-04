<?php include "db.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>All Receipts</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* General Styles */
body { 
    font-family: 'Segoe UI', Arial, sans-serif; 
    background: #f4f6f9; 
    padding: 40px; 
}
.container { 
    max-width: 1000px; 
    margin: auto; 
    background: #ffffff; 
    padding: 30px; 
    border-radius: 10px; 
    box-shadow: 0 5px 20px rgba(0,0,0,0.1); 
}
h2 { 
    text-align: center; 
    margin-bottom: 20px; 
}
.top-bar { 
    display: flex; 
    justify-content: space-between; 
    margin-bottom: 20px; 
    gap: 10px; 
}
.create-btn { 
    background: #28a745; 
    color: white; 
    padding: 8px 14px; 
    border-radius: 6px; 
    text-decoration: none; 
    font-weight: 600; 
}
.create-btn:hover { 
    opacity: 0.9; 
}
table { 
    width: 100%; 
    border-collapse: collapse; 
}
th { 
    background: #007bff; 
    color: white; 
    padding: 10px; 
    text-align: left; 
}
td { 
    padding: 10px; 
    border-bottom: 1px solid #ddd; 
}
tr:hover { 
    background: #f2f2f2; 
}
.view-btn, .edit-btn, .delete-btn { 
    padding: 5px 10px; 
    border-radius: 5px; 
    text-decoration: none; 
    font-size: 13px; 
    margin-right: 5px; 
}
.view-btn { 
    background: #007bff; 
    color: white; 
}
.edit-btn { 
    background: #ffc107; 
    color: white; 
}
.delete-btn { 
    background: #dc3545; 
    color: white; 
}
.view-btn:hover, .edit-btn:hover, .delete-btn:hover { 
    opacity: 0.8; 
}
.empty { 
    text-align: center; 
    padding: 20px; 
    color: #777; 
}

/* ---------- Responsive Styles ---------- */

/* Mobile & Tablet Styles */
@media (max-width: 768px) {
    body {
        padding: 20px;
    }
    
    .container {
        padding: 20px;
    }
    
    h2 {
        font-size: 20px;
    }
    
    .top-bar {
        flex-direction: column;
        align-items: center;
    }

    .create-btn {
        width: 100%;
        margin-bottom: 10px;
        text-align: center;
    }

    table {
        width: 100%;
        display: block;
        overflow-x: auto;
    }

    th, td {
        font-size: 12px;
        padding: 8px;
    }

    .view-btn, .edit-btn, .delete-btn {
        display: inline-block;
        font-size: 12px;
        margin-top: 5px;
        padding: 6px 12px;
        width: 100%;
        text-align: center;
    }

    .view-btn, .edit-btn, .delete-btn {
        margin-bottom: 5px;
    }
}

/* Larger Screen (Desktops) */
@media (min-width: 769px) {
    table {
        width: 100%;
        table-layout: auto;
    }
}
</style>
</head>
<body>

<div class="container">
<h2>All Receipts</h2>

<div class="top-bar">
    <div>
        <a href="index.php" class="create-btn">+ Create New</a>
        <a href="delete_all_receipts.php" class="create-btn" style="background:#dc3545;">Delete All</a>
    </div>
</div>

<table>
<tr>
    <th>ID</th>
    <th>Customer Name</th>
    <th>Receipt #</th>
    <th>Date</th>
    <th>Prev Reading (m³)</th>
    <th>Current Reading (m³)</th>
    <th>Total (PHP)</th>
    <th>Actions</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM receipts ORDER BY id DESC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $store_name = urlencode($row['store_name']);
        $prev_reading = number_format((float)$row['previous_reading'], 2);
        $curr_reading = number_format((float)$row['current_reading'], 2);
        $total_amount = number_format((float)$row['total'], 2);

        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['store_name']) . "</td>
                <td>" . htmlspecialchars($row['receipt_number']) . "</td>
                <td>" . date('M d, Y', strtotime($row['receipt_date'])) . "</td>
                <td>{$prev_reading}</td>
                <td>{$curr_reading}</td>
                <td>{$total_amount}</td>
                <td>
                    <a class='view-btn' href='view_customer.php?store_name={$store_name}'>View</a>
                    <a class='edit-btn' href='edit_receipts.php?id=" . htmlspecialchars($row['id']) . "'>Edit</a>
                    <a class='delete-btn' href='delete_confirmation.php?id=" . htmlspecialchars($row['id']) . "'>Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8' class='empty'>No receipts found.</td></tr>";
}
?>

</table>
</div>

</body>
</html>