<?php
session_start();
include('../database/connection.php'); // Make sure to include your database connection here

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password storage
    $company_name = $_POST['company_name'];
    $business_type = $_POST['business_type'];
    $website = $_POST['website'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $address = $_POST['address'];

    // Handle file uploads
    $target_dir = "../uploads/";
    $aadhar_file = $target_dir . basename($_FILES["aadhar"]["name"]);
    $pan_file = $target_dir . basename($_FILES["pan"]["name"]);
    $photo_file = $target_dir . basename($_FILES["photo"]["name"]);

    if (
        move_uploaded_file($_FILES["aadhar"]["tmp_name"], $aadhar_file) &&
        move_uploaded_file($_FILES["pan"]["tmp_name"], $pan_file) &&
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_file)
    ) {

        // Insert client data into the database (with file paths)
        $sql = "INSERT INTO clients (full_name, username, email, mobile, password, company_name, business_type, website, city, state, address, aadhar, pan, photo, status) 
                VALUES ('$full_name', '$username', '$email', '$mobile', '$password', '$company_name', '$business_type', '$website', '$city', '$state', '$address', '$aadhar_file', '$pan_file', '$photo_file', 'pending')";

        if (mysqli_query($conn, $sql)) {
            echo "Client registered successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Sorry, there was an error uploading your files.";
    }
}
?>