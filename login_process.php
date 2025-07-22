<?php
include 'includes/db.php';
$username = $_POST['username'];
$password = $_POST['password'];
$result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
if ($row = $result->fetch_assoc()) {
    session_start();
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['role'] = $row['role'];
    header("Location: " . ($row['role'] == 'admin' ? 'admin/index.php' : 'writer/index.php'));
} else {
    echo "نام کاربری یا رمز اشتباه است.";
}
?>