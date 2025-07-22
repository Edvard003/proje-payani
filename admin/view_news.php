<?php include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'admin')
    header("Location: ../login.php"); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>مشاهده خبر</title>
    <style>
        body {
            font-family: Arial;
            background: #FFF5E1;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #FFFFFF;
            padding: 20px;
            border: 2px solid #FFD700;
            border-radius: 10px;
        }

        h2 {
            color: #FF0000;
        }

        img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        p {
            color: #555;
            line-height: 1.6;
        }

        .btn {
            background: #FF0000;
            color: #FFD700;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #CC0000;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        $id = $_GET['id'];
        $result = $conn->query("SELECT n.*, c.name FROM news n JOIN categories c ON n.category_id = c.id WHERE n.id=$id");
        $row = $result->fetch_assoc();
        echo "<h2>" . $row['title'] . "</h2>";
        echo "<img src='" . $row['image'] . "' class='img-fluid mb-3'>";
        echo "<p>" . $row['content'] . "</p>";
        ?>
        <a href="index.php?page=news" class="btn">بازگشت</a>
    </div>
</body>

</html>