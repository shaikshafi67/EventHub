<?php
require_once 'databaseConnection.php';  // Include your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password
    $company_name = $_POST['company_name'];
    $business_type = $_POST['business_type'];
    $website = $_POST['website'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $address = $_POST['address'];

    // File upload paths
    $aadhar_path = "uploads/" . basename($_FILES['aadhar']['name']);
    $pan_path = "uploads/" . basename($_FILES['pan']['name']);
    $photo_path = !empty($_FILES['photo']['name']) ? "uploads/" . basename($_FILES['photo']['name']) : null;

    // Move uploaded files
    move_uploaded_file($_FILES['aadhar']['tmp_name'], $aadhar_path);
    move_uploaded_file($_FILES['pan']['tmp_name'], $pan_path);
    if ($photo_path)
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);

    $stmt = $conn->prepare("INSERT INTO clients 
        (full_name, username, email, mobile, password, company_name, business_type, website, city, state, address, aadhar_file, pan_file, photo_file, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("ssssssssssssss", $full_name, $username, $email, $mobile, $password, $company_name, $business_type, $website, $city, $state, $address, $aadhar_path, $pan_path, $photo_path);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful!'); window.location.href='client_login.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>