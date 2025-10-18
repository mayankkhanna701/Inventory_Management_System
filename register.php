<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $role = 'user';

    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please login.'); window.location='login.html';</script>";
    } else {
        echo "<script>alert('Username already exists.'); window.location='register.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
