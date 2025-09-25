<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
    header("Location: change_password.php?error=Passwords do not match");
    exit();
}

$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE users SET password_hash = ?, first_login = 0 WHERE id = ?");
$stmt->bind_param("si", $password_hash, $user_id);

if ($stmt->execute()) {
    header("Location: dashboard.php?message=Password changed successfully");
    exit();
} else {
    header("Location: change_password.php?error=Error changing password");
    exit();
}

$stmt->close();
$conn->close();
?>