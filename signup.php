<?php
include 'databaseConnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $mobile = $_POST['mobile'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $sql = "INSERT INTO signup (full_name, username, email, password, mobile_number)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fullName, $username, $email, $hashedPassword, $mobile);

    if ($stmt->execute()) {
        echo "
        <script>
            alert('Signup successful! Please login.');
            // Redirect to index.html and show login form
            window.location.href = 'index.html?showLogin=true';
        </script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>