<?php
require_once 'databaseConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start output buffering to prevent header issues
    ob_start();

    $event_id = intval($_POST['event_id']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $notes = trim($_POST['notes']);
    $tickets = intval($_POST['tickets']);
    $transaction_id = trim($_POST['transaction_id']);
    $total_amount = 0;

    // Fetch event price
    $stmt = $conn->prepare("SELECT event_price FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->bind_result($event_price);
    if ($stmt->fetch()) {
        $total_amount = $event_price * $tickets;
    } else {
        die("Invalid event.");
    }
    $stmt->close();

    // Handle payment proof upload
    $upload_dir = "uploads/";
    $payment_proof = "";
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['payment_proof']['tmp_name'];
        $file_name = uniqid("proof_") . "_" . basename($_FILES['payment_proof']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $payment_proof = $target_file;
        }
    }

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO event_bookings (event_id, full_name, email, mobile, notes, tickets, total_amount, transaction_id, payment_proof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssisss", $event_id, $full_name, $email, $mobile, $notes, $tickets, $total_amount, $transaction_id, $payment_proof);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        // Redirect to ticket page with booking ID
        header("Location: ticket.php?booking_id=" . $booking_id);
        exit();
    } else {
        echo "<script>alert('❌ Booking failed. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();

    ob_end_flush();
} else {
    die("Invalid request method.");
}
?>