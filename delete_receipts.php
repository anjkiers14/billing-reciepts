<?php
include "db.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Validate ID as positive integer
    if (filter_var($id, FILTER_VALIDATE_INT) && $id > 0) {

        // Prepare and execute delete statement
        $stmt = $conn->prepare("DELETE FROM receipts WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            // Redirect back to dashboard with a success message
            header("Location: dashboard.php?msg=deleted");
            exit;
        } else {
            echo "Error deleting receipt: " . htmlspecialchars($stmt->error);
        }
    } else {
        echo "Invalid receipt ID.";
    }
} else {
    echo "No receipt ID specified.";
}
?>
