<?php
include "db.php";

$error = "";
$FIXED_RATE = 2.00; // fixed rate per m³

// Check if receipt ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Receipt ID not specified.");
}

$id = (int)$_GET['id'];

// Fetch receipt safely
$stmt = $conn->prepare("SELECT * FROM receipts WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Receipt not found.");
}

$receipt = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $store_name = $_POST['store_name'];
    $receipt_number = $_POST['receipt_number'];
    $receipt_date = $_POST['receipt_date'];
    $previous_reading = (float)$_POST['previous_reading'];
    $current_reading = (float)$_POST['current_reading'];

    if ($current_reading < $previous_reading) {
        $error = "Current reading cannot be less than previous reading.";
    } else {

        $consumption = $current_reading - $previous_reading;
        $total = $consumption * $FIXED_RATE;

        $update = $conn->prepare("UPDATE receipts SET 
            store_name=?,
            receipt_number=?,
            receipt_date=?,
            previous_reading=?,
            current_reading=?,
            rate_per_cubic=?,
            total=? WHERE id=?");

        $update->bind_param(
            "sssddddi",
            $store_name,
            $receipt_number,
            $receipt_date,
            $previous_reading,
            $current_reading,
            $FIXED_RATE,
            $total,
            $id
        );

        if ($update->execute()) {
            header("Location: view_customer.php?store_name=" . urlencode($store_name));
            exit;
        } else {
            $error = "Error updating receipt.";
        }
    }
}

// Calculate values for display
$consumption_display = max(0, $receipt['current_reading'] - $receipt['previous_reading']);
$total_display = $consumption_display * $FIXED_RATE;
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Receipt</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body { font-family: 'Segoe UI', Arial, sans-serif; background:#f4f6f9; margin:0; }
.container { max-width:500px; margin:40px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1);}
.top-bar { margin-bottom:10px; }
.back-link { text-decoration:none; font-weight:600; color:#6c757d; font-size:14px; }
.back-link:hover { color:#000; }
h2 { margin:0 0 15px 0; color:#333; text-align:center; }
label { display:block; margin-top:12px; font-weight:600; }
input { width:100%; padding:10px; margin-top:6px; border-radius:6px; border:1px solid #ccc; font-size:14px; transition:0.2s; }
input:focus { border-color:#007bff; outline:none; box-shadow:0 0 5px rgba(0,123,255,0.3);}
input[readonly] { background:#e9ecef; cursor:not-allowed; }
button { padding:10px; border:none; border-radius:6px; font-weight:600; cursor:pointer; width:48%; }
.save-btn { background:#007bff; color:white; }
.print-btn { background:#28a745; color:white; }
.actions { display:flex; justify-content:space-between; margin-top:15px; }
.error-message { color:red; font-weight:600; text-align:center; margin-bottom:15px; }

/* Print Style */
@media print {
    @page { margin:0; }
    body { background:white; font-family:'Courier New', monospace; font-size:12px; display:flex; justify-content:center;}
    button, a { display:none !important; }
    .container { width:260px; padding:5px; margin:0 auto; box-shadow:none; border:none; text-align:center; }
    input { border:none; font-size:11px; text-align:center; padding:1px; }
}
</style>

<script>
const FIXED_RATE = 2.00;

function calculateBilling() {
    const prev = parseFloat(document.getElementById("previous_reading").value) || 0;
    const curr = parseFloat(document.getElementById("current_reading").value) || 0;

    let consumption = curr - prev;
    if (consumption < 0) consumption = 0;

    const total = consumption * FIXED_RATE;

    document.getElementById("consumption").value = consumption.toFixed(2);
    document.getElementById("total").value = total.toFixed(2);
    document.getElementById("rate_per_cubic").value = FIXED_RATE.toFixed(2);
}

function printReceipt() {
    calculateBilling();
    window.print();
}
</script>

</head>
<body>

<div class="container">

    <div class="top-bar">
        <a href="view_receipts.php" class="back-link">← Back</a>
    </div>

    <h2>BACIWA</h2>
    <p>Bacolod Water Billing</p>

    <?php if (!empty($error)) : ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>

        <label>Customer Name</label>
        <input type="text" name="store_name" value="<?= htmlspecialchars($receipt['store_name']) ?>" required>

        <label>Receipt Number</label>
        <input type="text" name="receipt_number" value="<?= htmlspecialchars($receipt['receipt_number']) ?>" required>

        <label>Receipt Date</label>
        <input type="date" name="receipt_date" value="<?= $receipt['receipt_date'] ?>" required>

        <hr>

        <label>Previous Reading (m³)</label>
        <input type="number" step="0.01" id="previous_reading" name="previous_reading"
               value="<?= $receipt['previous_reading'] ?>" oninput="calculateBilling()" required>

        <label>Current Reading (m³)</label>
        <input type="number" step="0.01" id="current_reading" name="current_reading"
               value="<?= $receipt['current_reading'] ?>" oninput="calculateBilling()" required>

        <label>Consumption (m³)</label>
        <input type="number" step="0.01" id="consumption" value="<?= $consumption_display ?>" readonly>

        <label>Rate per m³ (PHP)</label>
        <input type="number" step="0.01" id="rate_per_cubic" name="rate_per_cubic" value="<?= $FIXED_RATE ?>" readonly>

        <label>Total (PHP)</label>
        <input type="number" step="0.01" id="total" value="<?= number_format($total_display, 2, '.', '') ?>" readonly>

        <div class="actions">
            <button type="submit" class="save-btn">Save</button>
            <button type="button" class="print-btn" onclick="printReceipt()">Print</button>
        </div>

    </form>
</div>

<script>
calculateBilling(); // initial calculation
</script>

</body>
</html>