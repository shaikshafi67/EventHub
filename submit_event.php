<?php
// Database connection
require_once 'databaseConnection.php'; // Update path if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $event_name = $_POST['event_name'];
    $venue = $_POST['venue'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $speaker = $_POST['speaker'] ?? null;
    $event_description = $_POST['event_description'];
    $event_category = $_POST['event_category'];
    $other_category = $_POST['other_category'] ?? null;
    $organizer_phone = $_POST['organizer_phone'];
    $total_seats = $_POST['total_seats'];
    $max_seats = $_POST['max_seats'];
    $waitlist = isset($_POST['waitlist']) ? 1 : 0;
    $terms = isset($_POST['terms']) ? 1 : 0;
    $date_signed = $_POST['date_signed'];

    $payment_status = $_POST['payment_status'] ?? 'unpaid';
    $event_price = ($payment_status === 'paid' && isset($_POST['event_price'])) ? $_POST['event_price'] : 0.00;

    $event_status = 'pending'; // Default status when event is submitted

    // Handle file upload
    if (isset($_FILES['event_poster']) && $_FILES['event_poster']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = basename($_FILES['event_poster']['name']);
        $file_path = $upload_dir . uniqid() . '_' . $file_name;

        if (!move_uploaded_file($_FILES['event_poster']['tmp_name'], $file_path)) {
            die("Failed to upload event poster.");
        }
    } else {
        die("Event poster is required.");
    }

    // Insert into database
    $stmt = $conn->prepare("
        INSERT INTO events (
            event_name, venue, event_date, start_time, end_time, speaker, event_description,
            event_category, other_category, organizer_phone, event_poster,
            total_seats, max_seats, waitlist, terms, date_signed,
            payment_status, event_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssiiiissd",
        $event_name,
        $venue,
        $event_date,
        $start_time,
        $end_time,
        $speaker,
        $event_description,
        $event_category,
        $other_category,
        $organizer_phone,
        $file_path,
        $total_seats,
        $max_seats,
        $waitlist,
        $terms,
        $date_signed,
        $payment_status,
        $event_price
    );

    if ($stmt->execute()) {
        echo "<script>alert('Admin Approval is pending'); window.location.href='main.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>