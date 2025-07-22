<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['user_id'])) header("Location: ../login.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $news_id = $_GET['news_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO comments (news_id, user_id, comment) VALUES ($news_id, $user_id, '$comment')");
    header("Location: ../pages/news.php?id=$news_id");
}
?>