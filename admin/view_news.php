<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['news_id'])) {

    $news_id = filter_input(INPUT_POST, 'news_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if ($news_id === false || !in_array($action, ['approve', 'reject', 'delete'])) {
        header("Location: view_news.php?error=ورودی نامعتبر است");
        exit();
    }

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
        if ($stmt === false) {
            error_log("خطا در آماده‌سازی کوئری DELETE news: " . $conn->error);
            header("Location: view_news.php?error=خطا در حذف خبر");
            exit();
        }
        $stmt->bind_param("i", $news_id);
    } else {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE news SET status = ? WHERE id = ?");
        if ($stmt === false) {
            error_log("خطا در آماده‌سازی کوئری UPDATE news: " . $conn->error);
            header("Location: view_news.php?error=خطا در به‌روزرسانی وضعیت خبر");
            exit();
        }
        $stmt->bind_param("si", $status, $news_id);
    }

    if ($stmt->execute()) {
        $message = ($action === 'delete') ? 'حذف' : (($status === 'approved') ? 'تأیید' : 'رد');
        header("Location: view_news.php?success=خبر با موفقیت $message شد");
    } else {
        error_log("خطا در اجرای کوئری: " . $stmt->error);
        header("Location: view_news.php?error=خطا در پردازش درخواست");
    }
    $stmt->close();
    exit();
}

$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$start = ($page - 1) * $per_page;

$total_query = "SELECT COUNT(*) FROM news";
$total_result = $conn->query($total_query);
$total_news = $total_result->fetch_row()[0];
$total_pages = ceil($total_news / $per_page);

$query = "SELECT n.id, n.title, n.content, n.image, n.date, n.status, c.name AS category_name, u.full_name AS author_name 
        FROM news n 
        JOIN categories c ON n.category_id = c.id 
        JOIN users u ON n.author_id = u.id 
        ORDER BY n.date DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    error_log("خطا در آماده‌سازی کوئری SELECT news: " . $conn->error);
    header("Location: view_news.php?error=خطا در بارگذاری اخبار");
    exit();
}
$stmt->bind_param("ii", $start, $per_page);
$stmt->execute();
$news = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بررسی اخبار</title>
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

        @media (max-width: 768px) {
            .news-grid {
                grid-template-columns: 1fr;
            }
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

        .status-approved {
            color: #00FF00;
        }

        .status-rejected {
            color: #FF5555;
        }

        h2 {
            color: #FF0000;
            text-align: center;
            margin-bottom: 20px;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .page-link {
            background: #333333;
            color: #FFD700;
            border: 1px solid #FF0000;
        }

        .page-link:hover {
            background: #FF0000;
            color: #FFFFFF;
        }

        .page-item.active .page-link {
            background: #FF0000;
            color: #FFFFFF;
            border: 1px solid #FF0000;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>بررسی اخبار</h2>
        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=users">کاربران</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="view_news.php"> اخبار</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="review_news.php">تأیید اخبار</a>
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
            <?php if ($news->num_rows > 0): ?>
                <?php while ($row = $news->fetch_assoc()): ?>
                    <?php
                    $imagePath = !empty($row['image']) ? '/images/' . basename($row['image']) : '/images/default.jpg';
                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                        error_log("تصویر یافت نشد در $imagePath برای خبر: " . ($row['title'] ?? 'بدون عنوان'));
                        $imagePath = '/images/default.jpg';
                    }
                    $status_class = 'status-' . $row['status'];
                    ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($imagePath); ?>"
                            alt="<?php echo htmlspecialchars($row['title'] ?? 'بدون عنوان'); ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($row['title'] ?? 'بدون عنوان'); ?>
                            </h5>
                            <p class="card-text">
                                <?php echo htmlspecialchars(substr($row['content'] ?? '', 0, 100)) . '...'; ?>
                            </p>
                            <p class="card-text">نویسنده:
                                <?php echo htmlspecialchars($row['author_name'] ?? 'ناشناس'); ?>
                            </p>
                            <p class="card-text">دسته‌بندی:
                                <?php echo htmlspecialchars($row['category_name'] ?? 'بدون دسته‌بندی'); ?>
                            </p>
                            <p class="card-text">تاریخ:
                                <?php echo date('Y-m-d H:i', strtotime($row['date'])); ?>
                            </p>
                            <p class="card-text">وضعیت: <span class="<?php echo $status_class; ?>">
                                    <?php echo $row['status'] == 'pending' ? 'در انتظار' : ($row['status'] == 'approved' ? 'تأیید شده' : 'رد شده'); ?>
                                </span></p>
                            <a href="edit_news.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">ویرایش</a>
                            <form method="POST" action="view_news.php" style="display: inline;"
                                onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این خبر را حذف کنید؟');">
                                <input type="hidden" name="news_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <button type="submit" class="btn btn-danger">حذف</button>
                            </form>
                            <?php if ($row['status'] != 'approved'): ?>
                                <form method="POST" action="view_news.php" style="display: inline;">
                                    <input type="hidden" name="news_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit" class="btn btn-success">تأیید</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($row['status'] != 'rejected'): ?>
                                <form method="POST" action="view_news.php" style="display: inline;">
                                    <input type="hidden" name="news_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit" class="btn btn-danger">رد</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #FF5555; text-align: center;">هیچ خبری یافت نشد.</p>
            <?php endif; ?>
        </div>

        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <div style="text-align: center; margin-top: 20px;">
            <a href="../index.php" class="btn">بازگشت به خانه</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $stmt->close(); ?>