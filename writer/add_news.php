<?php
include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'writer')
    header("Location: ../login.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = $_POST['image'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO news (title, content, image, category_id, author_id, status) VALUES ('$title', '$content', '$image', $category_id, $user_id, 'pending')");
    header("Location: /news_project/writer/index.php");
}
?>