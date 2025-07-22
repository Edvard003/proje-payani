<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>دسته‌بندی</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFF5E1;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }

        .search-bar {
            background: #FF6347;
            padding: 20px;
            border-radius: 10px;
            color: #FFD700;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .form-control {
            border: 1px solid #FFD700;
            border-radius: 5px;
            margin-bottom: 10px;
            max-width: 300px;
        }

        .card {
            min-width: 250px;
            margin: 10px 0;
            background: #1A1A1A;
            border: 2px solid #FFD700;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            height: auto;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .card-img-top {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px 5px 0 0;
        }

        .card-body {
            padding: 15px;
            background: #333;
            color: #FFD700;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.2em;
            margin-bottom: 10px;
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
            flex-grow: 1;
        }

        .btn {
            background-color: #FF0000;
            color: #FFD700;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            align-self: flex-end;
        }

        .btn:hover {
            background-color: #CC0000;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding-bottom: 15px;
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
                <h4>جستجو</h4>
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
                $query = "SELECT n.*, c.name AS category_name, u.full_name AS author_name FROM news n JOIN categories c ON n.category_id = c.id JOIN users u ON n.author_id = u.id WHERE n.status = 'approved' ORDER BY n.date DESC";
                if ($cat_id && $cat_id !== 'all') {
                    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
                    $stmt->bind_param("i", $cat_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $cat_name = $result->fetch_assoc()['name'] ?? 'دسته‌بندی نامعتبر';
                    $stmt->close();
                    $query = "SELECT n.*, c.name AS category_name, u.full_name AS author_name FROM news n JOIN categories c ON n.category_id = c.id JOIN users u ON n.author_id = u.id WHERE c.id = ? AND n.status = 'approved' ORDER BY n.date DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $cat_id);
                    $stmt->execute();
                    $news = $stmt->get_result();
                } else {
                    $news = $conn->query($query);
                }
                if ($news->num_rows > 0) {
                    echo "<h3 style='color: #FFFFFF; text-align: center; margin: 20px 0; background: #FF4500; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);'>" . htmlspecialchars($cat_name) . "</h3>";
                    echo "<div class='news-grid'>";
                    while ($row = $news->fetch_assoc()) {
                        $rawImage = $row['image'];
                        $imagePath = !empty($row['image']) ? (preg_match('/^\/images\//', $row['image']) ? $row['image'] : '/images/' . htmlspecialchars(basename($row['image']))) : '/images/default.jpg';
                        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
                        if (!file_exists($fullPath)) {
                            error_log("تصویر یافت نشد در $fullPath برای خبر: " . $row['title']);
                            $imagePath = '/images/default.jpg';
                        }
                        echo "<div class='card'>
                                <img src='" . $imagePath . "' alt='" . htmlspecialchars($row['title']) . "' class='card-img-top' 
                                    onerror=\"this.src='/images/default.jpg'; console.log('خطا در بارگذاری تصویر کارت: ' + this.src + ', مسیر خام: " . addslashes($row['image']) . "');\">
                                <div class='card-body'>
                                    <h5 class='card-title'>" . htmlspecialchars($row['title']) . "</h5>
                                    <p class='card-author'>" . htmlspecialchars($row['author_name']) . "</p>
                                    <p class='card-date'>" . htmlspecialchars(date('Y-m-d', strtotime($row['date']))) . "</p>
                                    <p class='card-text'>" . htmlspecialchars(substr($row['content'], 0, 50)) . "...</p>
                                    <a href='news.php?id=" . $row['id'] . "' class='btn'>بیشتر</a>
                                </div>
                              </div>";
                    }
                    echo "</div>";
                } else {
                    echo "<p style='color: #FF0000; text-align: center;'>هیچ خبری در این دسته‌بندی یافت نشد.</p>";
                }
                if (isset($stmt)) {
                    $stmt->close();
                }
                ?>
            </div>
        </div>
        <a href="../index.php" class="btn" style="margin-top: 20px;">بازگشت به خانه</a>
    </div>
    <!-- CDN Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>