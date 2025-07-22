<?php
include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'admin')
    header("Location: ../login.php");
$id = $_GET['id'];
$conn->query("DELETE FROM news WHERE id=$id");
header("Location: index.php?page=news");
?>