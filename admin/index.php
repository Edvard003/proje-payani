<?php
include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'admin') header("Location: ../login.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پنل مدیریت</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFF5E1;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .row {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            background: #FF4500;
            padding: 20px;
            width: 30%;
            color: #FFD700;
        }
        .nav-item {
            margin: 10px 0;
        }
        .nav-link {
            color: #FFD700;
            text-decoration: none;
        }
        .nav-link:hover {
            color: #FFFFFF;
        }
        .content {
            padding: 20px;
            width: 70%;
            background: #FFFFFF;
            border-left: 2px solid #FFD700;
        }
        .card {
            background: #FF6347;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            color: #FFD700;
        }
        .btn {
            background-color: #FF0000;
            color: #FFD700;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-left: 5px;
        }
        .btn:hover {
            background-color: #CC0000;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="sidebar">
            <h4>منو</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="?page=users" class="nav-link">کاربران</a></li>
                <li class="nav-item"><a href="?page=news" class="nav-link">اخبار</a></li>
                <li class="nav-item"><a href="?page=categories" class="nav-link">دسته‌بندی‌ها</a></li>
                <li class="nav-item"><a href="?page=comments" class="nav-link">نظرات</a></li>
            </ul>
        </div>
        <div class="content">
            <?php
            $page = $_GET['page'] ?? 'users';
            if ($page == 'users') {
                $result = $conn->query("SELECT * FROM users");
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'>
                            " . $row['full_name'] . " (" . $row['role'] . ")
                            <a href='edit_user.php?id=" . $row['id'] . "' class='btn btn-warning'>ویرایش</a>
                            <a href='delete_user.php?id=" . $row['id'] . "' class='btn btn-danger'>حذف</a>
                          </div>";
                }
            } elseif ($page == 'news') {
                $result = $conn->query("SELECT n.*, c.name, u.full_name AS author_name FROM news n JOIN categories c ON n.category_id = c.id JOIN users u ON n.author_id = u.id WHERE n.status='pending'");
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'>
                            " . $row['title'] . " (نویسنده: " . $row['author_name'] . ")
                            <a href='edit_news.php?id=" . $row['id'] . "' class='btn btn-warning'>ویرایش</a>
                            <a href='delete_news.php?id=" . $row['id'] . "' class='btn btn-danger'>حذف</a>
                            <a href='view_news.php?id=" . $row['id'] . "' class='btn btn-info'>مشاهده</a>
                          </div>";
                }
            } elseif ($page == 'categories') {
                echo "<form method='POST' action='add_category.php'>
                        <input type='text' name='name' class='form-control mb-2' placeholder='نام دسته' style='border: 1px solid #FFD700;'>
                        <textarea name='description' class='form-control mb-2' placeholder='توضیحات' style='border: 1px solid #FFD700;'></textarea>
                        <button type='submit' class='btn'>ایجاد</button>
                      </form>";
            } elseif ($page == 'comments') {
                $result = $conn->query("SELECT c.*, n.title, u.full_name FROM comments c JOIN news n ON c.news_id = n.id JOIN users u ON c.user_id = u.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'>
                            خبر: " . $row['title'] . "<br>نظر: " . $row['comment'] . " (توسط: " . $row['full_name'] . ")
                            <a href='delete_comment.php?id=" . $row['id'] . "' class='btn btn-danger'>حذف</a>
                          </div>";
                }
            }
            ?>
            <a href="../index.php" class="btn" style="margin-top: 20px;">بازگشت به خانه</a>
        </div>
    </div>
</body>
</html>