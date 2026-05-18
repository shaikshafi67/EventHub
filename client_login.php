<?php
require_once 'databaseConnection.php';  // Include your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM clients WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $user, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION['client_id'] = $id;
            $_SESSION['username'] = $user;
            echo "<script>alert('Login Successful!'); window.location.href='client_event_form.html';</script>";
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='client_login.html';</script>";
        }
    } else {
        echo "<script>alert('Username not found.'); window.location.href='client_login.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>