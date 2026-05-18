<?php
session_start();
include 'databaseConnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];

    $sql = "SELECT * FROM signup WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];

            // Insert login details into the login table
            $loginSql = "INSERT INTO login (username) VALUES (?)";
            $loginStmt = $conn->prepare($loginSql);
            $loginStmt->bind_param("s", $username);
            $loginStmt->execute();
            $loginStmt->close();

            header("Location: main.php"); // Redirect to main page
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>