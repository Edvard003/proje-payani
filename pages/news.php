<?php
include '../includes/db.php';
session_start(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: news.php?id=" . ($_POST['news_id'] ?? 0) . "&error=خطای امنیتی CSRF");
        exit();
    }
    $news_id = filter_input(INPUT_POST, 'news_id', FILTER_VALIDATE_INT);
    if ($news_id === false) {
        header("Location: news.php?id=" . ($_POST['news_id'] ?? 0) . "&error=شناسه خبر نامعتبر است");
        exit();
    }
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    if ($stmt === false) {
        error_log("خطا در آماده‌سازی کوئری DELETE news: " . $conn->error);
        header("Location: news.php?id=$news_id&error=خطا در حذف خبر");
        exit();
    }
    $stmt->bind_param("i", $news_id);
    if ($stmt->execute()) {
        header("Location: ../index.php?success=خبر با موفقیت حذف شد");
    } else {
        error_log("خطا در اجرای کوئری DELETE: " . $stmt->error);
        header("Location: news.php?id=$news_id&error=خطا در حذف خبر");
    }
    $stmt->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جزئیات خبر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.0/dist/css/vazirmatn.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Vazirmatn', Arial, sans-serif;
            background-color: #121212;
            margin: 0;
            padding: 20px;
            color: #FFD700;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #1A1A1A;
            padding: 20px;
            border: 2px solid #FF0000;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        h2 {
            color: #FF0000;
            text-align: center;
            font-size: 2em;
            margin-bottom: 15px;
        }

        img {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 2px solid #FF0000;
        }

        .meta-info {
            color: #FFD700;
            font-size: 0.9em;
            margin-bottom: 15px;
            text-align: center;
        }

        .content {
            color: #FFD700;
            line-height: 1.8;
            font-size: 1em;
        }

        h4 {
            color: #FF0000;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .comment {
            background: #333333;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            color: #FFD700;
            border: 1px solid #FF0000;
        }

        .form-control {
            border: 1px solid #FF0000;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #222222;
            color: #FFD700;
            padding: 10px;
        }

        .form-control::placeholder {
            color: #FFD700;
            opacity: 0.7;
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

        .btn-danger {
            background: #FF5555;
        }

        .btn-danger:hover {
            background: #CC3333;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            img {
                max-height: 300px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== false && $id > 0) {
            $stmt = $conn->prepare("SELECT n.id, n.title, n.content, n.image, n.date, n.status, c.name AS category_name, u.full_name AS author_name 
                                    FROM news n 
                                    JOIN categories c ON n.category_id = c.id 
                                    JOIN users u ON n.author_id = u.id 
                                    WHERE n.id = ? AND n.status = 'approved'");
            if ($stmt === false) {
                error_log("خطا در آماده‌سازی کوئری SELECT news: " . $conn->error);
                echo "<p style='color: #FF5555; text-align: center;'>خطا در بارگذاری خبر</p>";
                exit();
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                $imagePath = !empty($row['image']) ? '/images/' . basename($row['image']) : '/images/default.jpg';
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                    error_log("تصویر یافت نشد در $imagePath برای خبر: " . ($row['title'] ?? 'بدون عنوان'));
                    $imagePath = '/images/default.jpg';
                }
                echo "<img src='" . htmlspecialchars($imagePath) . "' alt='" . htmlspecialchars($row['title'] ?? 'بدون عنوان') . "' class='img-fluid mb-3' onerror=\"this.src='/images/default.jpg';\">";
                $author = htmlspecialchars($row['author_name'] ?? 'نویسنده نامشخص');
                $category = htmlspecialchars($row['category_name'] ?? 'بدون دسته‌بندی');
                $date = date('Y-m-d H:i', strtotime($row['date']));
                echo "<div class='meta-info'>نویسنده: $author | دسته‌بندی: $category | تاریخ: $date</div>";
                echo "<div class='content'>" . nl2br(htmlspecialchars($row['content'])) . "</div>";

                if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                    echo "<form method='POST' action='news.php' style='margin-top: 20px; text-align: center;' onsubmit=\"return confirm('آیا مطمئن هستید که می‌خواهید این خبر را حذف کنید؟');\">";
                    echo "<input type='hidden' name='news_id' value='$id'>";
                    echo "<input type='hidden' name='action' value='delete'>";
                    echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
                    echo "<button type='submit' class='btn btn-danger'>حذف خبر</button>";
                    echo "</form>";
                }

                echo "<h4>نظرات</h4>";
                $stmt_comments = $conn->prepare("SELECT c.comment, c.date, u.full_name 
                                                FROM comments c 
                                                JOIN users u ON c.user_id = u.id 
                                                WHERE c.news_id = ? 
                                                ORDER BY c.date DESC");
                if ($stmt_comments === false) {
                    error_log("خطا در آماده‌سازی کوئری SELECT comments: " . $conn->error);
                    echo "<p style='color: #FF5555;'>خطا در بارگذاری نظرات</p>";
                } else {
                    $stmt_comments->bind_param("i", $id);
                    $stmt_comments->execute();
                    $comment_result = $stmt_comments->get_result();
                    if ($comment_result->num_rows > 0) {
                        while ($comment = $comment_result->fetch_assoc()) {
                            $comment_date = date('Y-m-d H:i', strtotime($comment['date']));
                            echo "<div class='comment'>" . htmlspecialchars($comment['comment']) . "<br><small>توسط: " . htmlspecialchars($comment['full_name']) . " | تاریخ: $comment_date</small></div>";
                        }
                    } else {
                        echo "<p style='color: #FFD700;'>هنوز نظری ثبت نشده است.</p>";
                    }
                    $stmt_comments->close();
                }

                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                    echo "<form method='POST' action='comment.php?news_id=$id' style='margin-top: 20px;'>";
                    echo "<textarea name='comment' class='form-control' placeholder='نظر خود را بنویسید...' required></textarea>";
                    echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
                    echo "<button type='submit' class='btn'>ارسال نظر</button>";
                    echo "</form>";
                } else {
                    error_log("سشن user_id در news.php تنظیم نشده است: " . print_r($_SESSION, true));
                    echo "<p style='color: #FF5555; text-align: center;'>برای ارسال نظر باید <a href='../login.php?redirect=news.php?id=$id' style='color: #FFD700;'>وارد شوید</a>.</p>";
                }
            } else {
                echo "<p style='color: #FF5555; text-align: center;'>خبر یافت نشد یا تأیید نشده است!</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: #FF5555; text-align: center;'>شناسه خبر نامعتبر است!</p>";
        }
        ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <div style="text-align: center; margin-top: 20px;">
            <a href="../index.php" class="btn">بازگشت به خانه</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>