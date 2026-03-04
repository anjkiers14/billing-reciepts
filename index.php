<?php
include "db.php";

/* Get next auto increment value */
$result = $conn->query("
    SELECT AUTO_INCREMENT 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'receipts'
");
$row = $result->fetch_assoc();
$next_id = $row['AUTO_INCREMENT'];

/* Generate receipt number */
$receipt_number = "BACIWA-" . date("Y") . "-" . str_pad($next_id, 5, "0", STR_PAD_LEFT);

/* Fetch customers from DB */
$customers = [];
$res = $conn->query("SELECT id, store_name FROM receipts ORDER BY store_name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $customers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Water Billing Receipt</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* ---------- Styles ---------- */
body {  
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #e3f2fd, #f4f6f9);
    margin: 0;
    overflow-x: hidden;
}
body::before { content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: url('baciwa_logo.jpg') no-repeat center center / cover; filter: blur(8px); z-index: -1; }
body::after { content: ""; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.2); z-index: -1; }

.navbar { background: #007bff; padding: 15px 25px; color: white; font-size: 18px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; box-sizing: border-box; }
.hamburger { display: flex; flex-direction: column; cursor: pointer; z-index: 1100; }
.hamburger span { height: 3px; width: 25px; background: white; margin: 4px 0; transition: 0.3s; }
.hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
.hamburger.active span:nth-child(2) { opacity: 0; }
.hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(6px, -6px); }
.nav-links { position: absolute; top: 60px; right: 25px; background: #007bff; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; max-height: 0; transition: max-height 0.3s ease; }
.nav-links a { color: white; text-decoration: none; padding: 12px 20px; font-size: 14px; }
.nav-links a:hover { background: rgba(255,255,255,0.2); }
.nav-links.active { max-height: 200px; }

.container { max-width: 500px; margin: 120px auto 40px auto; background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; }

label { font-weight: 600; display: block; margin-top: 12px; text-align: left; }
input, select { width: 100%; padding: 10px; margin-top: 6px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; }
input:focus, select:focus { border-color: #007bff; outline: none; box-shadow: 0 0 5px rgba(0,123,255,0.3); }
button { padding: 10px 15px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 15px; width: 48%; }
.save-btn { background: #007bff; color: white; }
.print-btn { background: #343a40; color: white; }
button:hover { opacity: 0.9; }

@media print {
    body {
        background: white;
        font-family: 'Courier New', monospace;
        font-size: 10px; /* Reduced font size to fit more content */
        margin: 0; /* Remove body margin */
        padding: 0;
        width: 100%; /* Ensure the body takes full page width */
        box-sizing: border-box;
    }

    .navbar, .footer, button {
        display: none !important; /* Hide unnecessary elements */
    }

    body::before, body::after {
        display: none;
    }

    .container {
        width: 100%; /* Use the full width available */
        max-width: 380px; /* Maintain max width */
        padding: 5px; /* Reduce padding for more space */
        margin: 0; /* Ensure no margin */
        box-shadow: none;
        border: none;
        text-align: left;
    }

    label {
        text-align: left;
        font-size: 9px; /* Adjust label font size */
        display: block;
        margin-top: 6px; /* Adjust margin */
        word-wrap: break-word;
    }

    input, select {
        border: none;
        font-size: 9px; /* Adjust input font size */
        text-align: left;
        padding: 2px;
        width: 100%;
        box-sizing: border-box;
        word-wrap: break-word;
    }
}
</style>

<script>
function fetchCustomerData(customerId) {
    if (!customerId) {
        document.getElementById('customer_address').value = "";
        document.getElementById('previous_reading').value = "";
        calculateBilling();
        return;
    }
    fetch('get_customer_data.php?id=' + customerId)
    .then(response => response.json())
    .then(data => {
        document.getElementById('customer_address').value = data.address || "";
        document.getElementById('previous_reading').value = data.previous_reading || 0;
        calculateBilling();
    })
    .catch(() => {
        alert("Error fetching customer data");
    });
}

function calculateBilling() {
    const prev = parseFloat(document.getElementById("previous_reading").value) || 0;
    const curr = parseFloat(document.getElementById("current_reading").value) || 0;
    const rate = 2;
    let consumption = curr - prev;
    if (consumption < 0) consumption = 0;
    const amountDue = consumption * rate;
    document.getElementById("consumption").value = consumption.toFixed(2);
    document.getElementById("amount_due").value = amountDue.toFixed(2);
}

function printReceipt() {
    calculateBilling();
    window.print();
}

function toggleMenu() {
    document.getElementById("navLinks").classList.toggle("active");
    document.getElementById("hamburger").classList.toggle("active");
}

document.addEventListener("DOMContentLoaded", function() {
    const links = document.querySelectorAll(".nav-links a");
    links.forEach(link => {
        link.addEventListener("click", () => {
            document.getElementById("navLinks").classList.remove("active");
            document.getElementById("hamburger").classList.remove("active");
        });
    });
});

// ---------- Customer selection ----------
function onCustomerSelect(storeName) {
    const options = document.querySelectorAll('#customer_list option');
    let customerId = '';
    for (let i = 0; i < options.length; i++) {
        if(options[i].value.toLowerCase() === storeName.toLowerCase()) {
            customerId = options[i].dataset.id;
            break;
        }
    }

    if(customerId) {
        document.getElementById('customer_id').value = customerId;
        fetchCustomerData(customerId);
    } else {
        document.getElementById('customer_id').value = '';
        document.getElementById('customer_address').value = '';
        document.getElementById('previous_reading').value = '';
        console.warn("Customer not found or name mismatch.");
    }
}
</script>
</head>

<body>

<div class="navbar">
    <div>Water Billing System</div>
    <div class="hamburger" id="hamburger" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </div>
    <div class="nav-links" id="navLinks">
        <a href="view_receipts.php">View Receipts</a>
        <a href="dashboard.php">Dashboard</a>
    </div>
</div>

<div class="container">
<div class="header">
    <h2>BACIWA</h2>
    <p>Corner Galo-San Juan St, Bacolod City, Negros Occ.</p>
</div>

<form action="save_receipt.php" method="POST">

<label>Receipt Number</label>
<input type="text" name="receipt_number" value="<?php echo $receipt_number; ?>" readonly>

<label for="customer">Customer</label>
<input list="customer_list" id="customer" name="customer_name" placeholder="Type or select a customer" onchange="onCustomerSelect(this.value)" required>
<input type="hidden" id="customer_id" name="customer_id">

<datalist id="customer_list">
    <?php foreach ($customers as $cust): ?>
        <option value="<?php echo htmlspecialchars($cust['store_name']); ?>" data-id="<?php echo $cust['id']; ?>"></option>
    <?php endforeach; ?> 
</datalist>

<label for="customer_address">Customer Address</label>

<input type="text"
       id="customer_address"
       name="customer_address"
       placeholder="Type or select address"
       list="address_list"
       autocomplete="off">

<datalist id="address_list">
    <option value="Barangay 35">
    <option value="Barangay 16">
    <option value="Barangay 3">
    <option value="Barangay 19">
    <option value="Barangay 20">

</datalist>

<label>Billing Date</label>
<input type="date" name="billing_date" value="<?php echo date('Y-m-d'); ?>" required>

<hr>

<label>Previous Reading (m³)</label>
<input type="number" step="0.01" id="previous_reading" name="previous_reading" min="0" readonly>

<label>Current Reading (m³)</label>
<input type="number" step="0.01" id="current_reading" name="current_reading" min="0" oninput="calculateBilling()" required>

<label>Consumption (m³)</label>
<input type="number" step="0.01" id="consumption" name="consumption" readonly>

<label>Rate per m³ (PHP)</label>
<input type="number" value="2" readonly>

<label>Amount Due (PHP)</label>
<input type="number" step="0.01" id="amount_due" name="amount_due" readonly>

<div style="display:flex; justify-content:space-between;">
    <button type="submit" class="save-btn">Save</button>
    <button type="button" class="print-btn" onclick="printReceipt()">Print</button>
</div>

</form>
</div>

<div class="footer" style="text-align:center; padding:15px; font-size:12px;">
    © 2026 BACIWA | PHP & MySQL
</div>

</body>
</html>