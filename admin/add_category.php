<?php
include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'admin')
    header("Location: ../login.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $conn->query("INSERT INTO categories (name, description) VALUES ('$name', '$description')");
    header("Location: index.php?page=categories");
}
?>