<?php
include '../includes/db.php';
session_start();

if (!$conn) {
    error_log("اتصال به دیتابیس برقرار نشد: " . mysqli_connect_error());
    die("<p style='color: #FF5555; text-align: center;'>خطا در اتصال به دیتابیس: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>دسته‌بندی</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
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

        .search-bar {
            background: #333333;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #FF0000;
            color: #FFD700;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .form-control {
            border: 1px solid #FF0000;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            max-width: 300px;
            background: #222222;
            color: #FFFFFF;
        }

        .form-control:focus {
            border-color: #FFD700;
            box-shadow: 0 0 5px rgba(255, 215, 0, 0.5);
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

        .card-author,
        .card-date {
            font-size: 0.9em;
            margin-bottom: 5px;
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
            align-self: center;
        }

        .btn:hover {
            background: #CC0000;
            transform: scale(1.05);
        }

        h3 {
            color: #FF0000;
            background: #333333;
            padding: 10px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }

        .debug-info {
            background: #333333;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #FFFFFF;
            text-align: center;
        }

        .alert {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchTitle = document.getElementById('search-title');
            const searchAuthor = document.getElementById('search-author');
            const searchDate = document.getElementById('search-date');

            function filterCards() {
                const titleValue = searchTitle.value.toLowerCase().trim();
                const authorValue = searchAuthor.value.toLowerCase().trim();
                const dateValue = searchDate.value.toLowerCase().trim();

                document.querySelectorAll('.card').forEach(card => {
                    const title = card.querySelector('.card-title').textContent.toLowerCase();
                    const author = card.querySelector('.card-author').textContent.toLowerCase();
                    const date = card.querySelector('.card-date').textContent.toLowerCase();

                    const matchesTitle = titleValue ? title.includes(titleValue) : true;
                    const matchesAuthor = authorValue ? author.includes(authorValue) : true;
                    const matchesDate = dateValue ? date.includes(dateValue) : true;

                    card.style.display = (matchesTitle && matchesAuthor && matchesDate) ? 'block' : 'none';
                });
            }

            if (searchTitle && searchAuthor && searchDate) {
                searchTitle.addEventListener('input', filterCards);
                searchAuthor.addEventListener('input', filterCards);
                searchDate.addEventListener('input', filterCards);
            } else {
                console.log('یکی از فیلدهای جستجو یافت نشد!');
            }
        });
    </script>
</head>

<body>
    <div class="container">
        <div class="search-bar">
            <div>
                <h4 style="color: #FF0000;">جستجو</h4>
                <input type="text" id="search-title" class="form-control" placeholder="جستجو بر اساس عنوان...">
                <input type="text" id="search-author" class="form-control" placeholder="جستجو بر اساس نویسنده...">
                <input type="text" id="search-date" class="form-control" placeholder="جستجو بر اساس تاریخ (YYYY-MM-DD)">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php
                $cat_id = $_GET['cat_id'] ?? null;
                $cat_name = 'همه دسته‌ها';
                $query = "SELECT n.*, c.name AS category_name, u.full_name AS author_name 
                        FROM news n 
                        LEFT JOIN categories c ON n.category_id = c.id 
                        LEFT JOIN users u ON n.author_id = u.id 
                        WHERE n.status = 'approved' 
                        ORDER BY n.date DESC";
                if ($cat_id && $cat_id !== 'all') {
                    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
                    $stmt->bind_param("i", $cat_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $cat_name = $result->fetch_assoc()['name'] ?? 'دسته‌بندی نامعتبر';
                    $stmt->close();
                    $query = "SELECT n.*, c.name AS category_name, u.full_name AS author_name 
                            FROM news n 
                            LEFT JOIN categories c ON n.category_id = c.id 
                            LEFT JOIN users u ON n.author_id = u.id 
                            WHERE c.id = ? AND n.status = 'approved' 
                            ORDER BY n.date DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $cat_id);
                    $stmt->execute();
                    $news = $stmt->get_result();
                } else {
                    $news = $conn->query($query);
                }
                if ($news && $news->num_rows > 0) {
                    echo "<div class='debug-info'>تعداد اخبار یافت‌شده: " . $news->num_rows . "</div>";
                    error_log("تعداد اخبار یافت‌شده در دسته‌بندی '$cat_name': " . $news->num_rows);
                    echo "<h3>" . htmlspecialchars($cat_name) . "</h3>";
                    echo "<div class='news-grid'>";
                    while ($row = $news->fetch_assoc()) {
                        $imagePath = !empty($row['image']) ? (preg_match('/^\/images\//', $row['image']) ? $row['image'] : '/images/' . htmlspecialchars(basename($row['image']))) : '/images/default.jpg';
                        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
                        if (!file_exists($fullPath)) {
                            error_log("تصویر یافت نشد در $fullPath برای خبر: " . ($row['title'] ?? 'بدون عنوان'));
                            $imagePath = '/images/default.jpg';
                        }
                        echo "<div class='card'>
                                <img src='" . htmlspecialchars($imagePath) . "' alt='" . htmlspecialchars($row['title'] ?? 'بدون عنوان') . "' class='card-img-top' 
                                    onerror=\"this.src='/images/default.jpg'; console.log('خطا در بارگذاری تصویر کارت: ' + this.src);\">
                                <div class='card-body'>
                                    <h5 class='card-title'>" . htmlspecialchars($row['title'] ?? 'بدون عنوان') . "</h5>
                                    <p class='card-author'>" . htmlspecialchars($row['author_name'] ?? ($row['author_id'] ?? 'ناشناس')) . "</p>
                                    <p class='card-date'>" . htmlspecialchars(date('Y-m-d', strtotime($row['date'] ?? 'now'))) . "</p>
                                    <p class='card-text'>" . htmlspecialchars(substr($row['content'] ?? '', 0, 50)) . "...</p>
                                    <a href='news.php?id=" . ($row['id'] ?? 0) . "' class='btn'>بیشتر</a>
                                </div>
                            </div>";
                    }
                    echo "</div>";
                } else {
                    echo "<p style='color: #FF5555; text-align: center;'>هیچ خبری در این دسته‌بندی یافت نشد.</p>";
                    error_log("هیچ خبری در دسته‌بندی '$cat_name' یافت نشد.");
                }
                if (isset($stmt)) {
                    $stmt->close();
                }
                $conn->close();
                ?>

            </div>
        </div>
        <a href="../index.php" class="btn" style="margin-top: 20px;">بازگشت به خانه</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>