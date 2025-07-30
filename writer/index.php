<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'writer') {
    header("Location: ../login.php");
    exit();
}
$author_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>پنل نویسنده</title>
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
        }

        .btn:hover {
            background: #CC0000;
            transform: scale(1.05);
        }

        .status-pending {
            color: #FFD700;
        }

        .status-approved {
            color: #00FF00;
        }

        .status-rejected {
            color: #FF5555;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="color: #FF0000; text-align: center; margin-bottom: 20px;">پنل نویسنده</h2>
        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">اخبار شما</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add_news.php">ایجاد خبر</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add_category.php">ایجاد دسته‌بندی</a>
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

        <h3 style="color: #FF4500; margin-top: 30px; text-align: center;">اخبار شما</h3>
        <div class="news-grid">
            <?php
            $stmt = $conn->prepare("SELECT n.*, c.name AS category_name FROM news n JOIN categories c ON n.category_id = c.id WHERE n.author_id = ? ORDER BY n.date DESC");
            $stmt->bind_param("i", $author_id);
            $stmt->execute();
            $news = $stmt->get_result();
            if ($news->num_rows > 0) {
                while ($row = $news->fetch_assoc()) {
                    $imagePath = !empty($row['image']) ? (preg_match('/^\/images\//', $row['image']) ? $row['image'] : '/images/' . htmlspecialchars(basename($row['image']))) : '/images/default.jpg';
                    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
                    if (!file_exists($fullPath)) {
                        error_log("تصویر یافت نشد در $fullPath برای خبر: " . $row['title']);
                        $imagePath = '/images/default.jpg';
                    }
                    $status_class = 'status-' . $row['status'];
                    $status_text = $row['status'] == 'approved' ? 'تأیید شده' : ($row['status'] == 'pending' ? 'در انتظار' : 'رد شده');
                    echo "<div class='card'>
                            <img src='" . $imagePath . "' alt='" . htmlspecialchars($row['title']) . "' class='card-img-top' 
                                onerror=\"this.src='/images/default.jpg'; console.log('خطا در بارگذاری تصویر کارت: ' + this.src + ', مسیر خام: " . addslashes($row['image']) . "');\">
                            <div class='card-body'>
                                <h5 class='card-title'>" . htmlspecialchars($row['title']) . "</h5>
                                <p class='card-text'>" . htmlspecialchars(substr($row['content'], 0, 50)) . "...</p>
                                <p class='card-text'>دسته‌بندی: " . htmlspecialchars($row['category_name']) . "</p>
                                <p class='card-text'>وضعیت: <span class='$status_class'>$status_text</span></p>
                                <a href='add_news.php?edit_id=" . $row['id'] . "' class='btn'>ویرایش</a>
                            </div>
                        </div>";
                }
            } else {
                echo "<p style='color: #FF5555; text-align: center;'>هیچ خبری توسط شما ثبت نشده است.</p>";
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