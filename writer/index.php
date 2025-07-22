<?php include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'writer')
    header("Location: ../login.php"); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>پنل نویسنده</title>
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

        .nav-link {
            color: #FFD700;
            text-decoration: none;
            margin: 0 10px;
        }

        .nav-link:hover {
            color: #FFFFFF;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="color: #FF0000;">پنل نویسنده</h2>
        <div style="margin-bottom: 20px;">
            <a href="index.php" class="nav-link">ایجاد خبر</a>
            <a href="add_category.php" class="nav-link">ایجاد دسته‌بندی</a>
        </div>
        <?php
        if (basename($_SERVER['PHP_SELF']) == 'index.php') {
            echo "<form method='POST' action='add_news.php'>
                    <input type='text' name='title' class='form-control' placeholder='عنوان خبر' required>
                    <textarea name='content' class='form-control' placeholder='محتوا' required></textarea>
                    <input type='text' name='image' class='form-control' placeholder='آدرس تصویر' required>
                    <select name='category_id' class='form-control' required>
                        " . join('', array_map(function ($cat) {
                return "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
            }, $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC))) . "
                    </select>
                    <button type='submit' class='btn'>ایجاد</button>
                  </form>";
        }
        ?>
    </div>
</body>

</html>