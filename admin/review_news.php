<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['news_id'])) {
    $news_id = $_POST['news_id'];
    $action = $_POST['action'];
    $status = ($action == 'approve') ? 'approved' : 'rejected';
    $stmt = $conn->prepare("UPDATE news SET status = ? WHERE id = ?");
    if ($stmt === false) {
        error_log("خطا در آماده‌سازی کوئری UPDATE news: " . $conn->error);
        header("Location: review_news.php?error=خطا در به‌روزرسانی وضعیت خبر");
        exit();
    }
    $stmt->bind_param("si", $status, $news_id);
    if ($stmt->execute()) {
        header("Location: review_news.php?success=خبر با موفقیت " . ($status == 'approved' ? 'تأیید' : 'رد') . " شد");
    } else {
        error_log("خطا در اجرای کوئری UPDATE news: " . $stmt->error);
        header("Location: review_news.php?error=خطا در به‌روزرسانی وضعیت خبر");
    }
    $stmt->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تأیید اخبار</title>
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

        .news-grid {
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

        .card-img-top {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            padding: 15px;
            background: #333333;
            color: #FFD700;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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

        .btn-success {
            background: #00FF00;
            color: #333;
        }

        .btn-success:hover {
            background: #00CC00;
        }

        .btn-danger {
            background: #FF5555;
        }

        .btn-danger:hover {
            background: #CC3333;
        }

        .status-pending {
            color: #FFD700;
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
        <h2>تأیید اخبار</h2>
        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=users">کاربران</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view_news.php">اخبار</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="review_news.php">تأیید اخبار</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=categories">دسته‌بندی‌ها</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=comments">نظرات</a>
            </li>
        </ul>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="news-grid">
            <?php
            $query = "SELECT n.*, c.name AS category_name, u.full_name AS author_name 
                    FROM news n 
                    JOIN categories c ON n.category_id = c.id 
                    JOIN users u ON n.author_id = u.id 
                    WHERE n.status = 'pending'";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                error_log("خطا در آماده‌سازی کوئری SELECT news: " . $conn->error);
                echo "<p style='color: #FF5555; text-align: center;'>خطا در بارگذاری اخبار</p>";
                exit();
            }
            $stmt->execute();
            $news = $stmt->get_result();
            if ($news->num_rows > 0) {
                while ($row = $news->fetch_assoc()) {
                    $imagePath = !empty($row['image']) ? (preg_match('/^\/images\//', $row['image']) ? $row['image'] : '/images/' . htmlspecialchars(basename($row['image']))) : '/images/default.jpg';
                    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
                    if (!file_exists($fullPath)) {
                        error_log("تصویر یافت نشد در $fullPath برای خبر: " . ($row['title'] ?? 'بدون عنوان'));
                        $imagePath = '/images/default.jpg';
                    }
                    echo "<div class='card'>
                            <img src='" . htmlspecialchars($imagePath) . "' alt='" . htmlspecialchars($row['title'] ?? 'بدون عنوان') . "' class='card-img-top'>
                            <div class='card-body'>
                                <h5 class='card-title'>" . htmlspecialchars($row['title'] ?? 'بدون عنوان') . "</h5>
                                <p class='card-text'>" . htmlspecialchars(substr($row['content'] ?? '', 0, 50)) . "...</p>
                                <p class='card-text'>نویسنده: " . htmlspecialchars($row['author_name'] ?? 'ناشناس') . "</p>
                                <p class='card-text'>دسته‌بندی: " . htmlspecialchars($row['category_name'] ?? 'بدون دسته‌بندی') . "</p>
                                <p class='card-text'>وضعیت: <span class='status-pending'>در انتظار</span></p>
                                <a href='edit_news.php?id=" . ($row['id'] ?? 0) . "' class='btn btn-warning'>ویرایش</a>
                                <a href='delete_news.php?id=" . ($row['id'] ?? 0) . "' class='btn btn-danger'>حذف</a>
                                <form method='POST' action='review_news.php' style='display: inline;'>
                                    <input type='hidden' name='news_id' value='" . ($row['id'] ?? 0) . "'>
                                    <button type='submit' name='action' value='approve' class='btn btn-success'>تأیید</button>
                                </form>
                                <form method='POST' action='review_news.php' style='display: inline;'>
                                    <input type='hidden' name='news_id' value='" . ($row['id'] ?? 0) . "'>
                                    <button type='submit' name='action' value='reject' class='btn btn-danger'>رد</button>
                                </form>
                            </div>
                        </div>";
                }
            } else {
                echo "<p style='color: #FF5555; text-align: center;'>هیچ خبری در انتظار تأیید نیست.</p>";
            }
            $stmt->close();
            ?>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="../index.php" class="btn">بازگشت به خانه</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>