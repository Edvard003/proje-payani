<?php
include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'writer')
    header("Location: ../login.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>ایجاد دسته‌بندی</title>
    <style>
        body {
            font-family: Arial;
            background: #FFF5E1;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 20px auto;
            background: #FFFFFF;
            padding: 20px;
            border: 2px solid #FFD700;
            border-radius: 10px;
        }

        .form-control {
            border: 1px solid #FFD700;
            border-radius: 5px;
            padding: 5px;
            width: 100%;
            margin-bottom: 10px;
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
        <h2 style="color: #FF0000;">ایجاد دسته‌بندی جدید</h2>
        <form method="POST" action="add_category.php">
            <input type="text" name="name" class="form-control" placeholder="نام دسته" required>
            <textarea name="description" class="form-control" placeholder="توضیحات" required></textarea>
            <button type="submit" class="btn">ایجاد</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $conn->query("INSERT INTO categories (name, description) VALUES ('$name', '$description')");
            echo "<p style='color: #FFD700;'>دسته‌بندی با موفقیت ایجاد شد!</p>";
            header("Location: /news_project/writer/index.php");
        }
        ?>
    </div>
</body>

</html>