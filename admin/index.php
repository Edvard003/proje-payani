<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>پنل مدیریت</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            margin: 0;
            padding: 20px;
            color: #FFD700;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #1A1A1A;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .nav-tabs {
            border-bottom: 2px solid #FF0000;
        }

        .nav-link {
            color: #FFD700;
            font-weight: bold;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #FF0000;
            color: #FFFFFF;
            border-radius: 10px 10px 0 0;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px 0;
        }

        .card {
            background: #222222;
            border: 2px solid #FF0000;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
        }

        .card-body {
            padding: 15px;
            background: #333333;
            color: #FFD700;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1.3em;
            margin-bottom: 10px;
            color: #FFFFFF;
        }

        .card-text {
            font-size: 0.9em;
            margin-bottom: 10px;
            color: #FFD700;
        }

        .btn {
            background: #FF0000;
            color: #FFFFFF;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin: 5px;
        }

        .btn:hover {
            background: #CC0000;
            transform: scale(1.05);
        }

        .btn-warning {
            background: #FFD700;
            color: #333;
        }

        .btn-warning:hover {
            background: #FFCC00;
        }

        .btn-info {
            background: #00BFFF;
            color: #FFFFFF;
        }

        .btn-info:hover {
            background: #009ACD;
        }

        .btn-danger {
            background: #FF5555;
        }

        .btn-danger:hover {
            background: #CC3333;
        }

        h2 {
            color: #FF0000;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>پنل مدیریت</h2>
        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link <?php echo ($_GET['page'] ?? 'users') == 'users' ? 'active' : ''; ?>"
                    href="?page=users">کاربران</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view_news.php">اخبار</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="review_news.php">تأیید اخبار</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_GET['page'] ?? '') == 'categories' ? 'active' : ''; ?>"
                    href="?page=categories">دسته‌بندی‌ها</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_GET['page'] ?? '') == 'comments' ? 'active' : ''; ?>"
                    href="?page=comments">نظرات</a>
            </li>
        </ul>

        <div class="content-grid">
            <?php
            $page = $_GET['page'] ?? 'users';
            if ($page == 'users') {
                $stmt = $conn->prepare("SELECT * FROM users");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'>
                            <div class='card-body'>
                                <h5 class='card-title'>" . htmlspecialchars($row['full_name']) . "</h5>
                                <p class='card-text'>نقش: " . ($row['role'] == 'admin' ? 'مدیر' : 'نویسنده') . "</p>
                                <p class='card-text'>نام کاربری: " . htmlspecialchars($row['username']) . "</p>
                                <a href='edit_user.php?id=" . $row['id'] . "' class='btn btn-warning'>ویرایش</a>
                                <a href='delete_user.php?id=" . $row['id'] . "' class='btn btn-danger'>حذف</a>
                            </div>
                        </div>";
                }
                $stmt->close();
            } elseif ($page == 'news') {
                header("Location: view_news.php");
                exit();
            } elseif ($page == 'categories') {
                echo "<div class='card'>
                        <div class='card-body'>
                            <a href='add_category.php' class='btn btn-primary'>ایجاد دسته‌بندی جدید</a>
                        </div>
                      </div>";
                $stmt = $conn->prepare("SELECT * FROM categories");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'>
                            <div class='card-body'>
                                <h5 class='card-title'>" . htmlspecialchars($row['name']) . "</h5>
                                <p class='card-text'>" . (empty($row['description']) ? 'بدون توضیحات' : htmlspecialchars(substr($row['description'], 0, 50)) . '...') . "</p>
                                <a href='edit_category.php?id=" . $row['id'] . "' class='btn btn-warning'>ویرایش</a>
                                <a href='delete_category.php?id=" . $row['id'] . "' class='btn btn-danger'>حذف</a>
                            </div>
                        </div>";
                }
                $stmt->close();
            } elseif ($page == 'comments') {
                $stmt = $conn->prepare("SELECT c.*, n.title, u.full_name FROM comments c JOIN news n ON c.news_id = n.id JOIN users u ON c.user_id = u.id");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'>
                            <div class='card-body'>
                                <h5 class='card-title'>خبر: " . htmlspecialchars($row['title']) . "</h5>
                                <p class='card-text'>نظر: " . htmlspecialchars(substr($row['comment'], 0, 50)) . "...</p>
                                <p class='card-text'>توسط: " . htmlspecialchars($row['full_name']) . "</p>
                                <a href='delete_comment.php?id=" . $row['id'] . "' class='btn btn-danger'>حذف</a>
                            </div>
                        </div>";
                }
                $stmt->close();
            }
            ?>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="../index.php" class="btn">بازگشت به خانه</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>