<?php
include 'includes/db.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$conn) {
    error_log("اتصال به دیتابیس برقرار نشد: " . mysqli_connect_error());
    die("<p style='color: #FF5555; text-align: center;'>خطا در اتصال به دیتابیس: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>اخبار ایران</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            margin: 0;
            padding: 0;
            color: #FFD700;
        }

        .navbar {
            background-color: #FF0000;
            padding: 15px;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-radius: 0 0 10px 10px;
        }

        .navbar-brand,
        .nav-link {
            color: #FFFFFF !important;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover,
        .nav-link:hover {
            color: #FFD700 !important;
        }

        .dropdown-menu {
            background-color: #1A1A1A;
            border: 1px solid #FF0000;
            border-radius: 10px;
        }

        .dropdown-item {
            color: #FFD700;
        }

        .dropdown-item:hover {
            background-color: #FF0000;
            color: #FFFFFF;
        }

        .carousel-container {
            width: 100%;
            margin: 20px auto;
            padding: 20px;
            background: #1A1A1A;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .image-carousel {
            width: 100%;
            height: 60vh;
            overflow: hidden;
            border-radius: 10px;
        }

        .image-carousel .carousel-item {
            height: 60vh;
            background-size: cover;
            background-position: center;
            position: relative;
            border-radius: 10px;
        }

        .text-carousel {
            width: 100%;
            background: #333333;
            padding: 20px;
            border-radius: 10px;
            margin-top: 10px;
        }

        .text-carousel .carousel-item {
            text-align: center;
            color: #FFD700;
            padding: 20px;
        }

        .text-carousel h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #FFFFFF;
        }

        .text-carousel p {
            font-size: 1em;
            margin-bottom: 15px;
            color: #FFD700;
        }

        .news-container {
            margin: 0 auto;
            padding: 20px;
            max-width: 1200px;
            text-align: center;
            background: #1A1A1A;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
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

        .card img {
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
        }

        .debug-info {
            background: #333333;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #FFFFFF;
            text-align: center;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">خانه</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            دسته‌بندی
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/category.php?cat_id=all">همه دسته‌ها</a></li>
                            <?php
                            $categories = $conn->query("SELECT id, name FROM categories ORDER BY id");
                            if ($categories) {
                                while ($row = $categories->fetch_assoc()) {
                                    echo "<li><a class='dropdown-item' href='pages/category.php?cat_id=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a></li>";
                                }
                                $categories->free();
                            } else {
                                error_log("خطا در بارگذاری دسته‌بندی‌ها: " . $conn->error);
                                echo "<li><a class='dropdown-item' href='#'>خطا در بارگذاری دسته‌بندی‌ها</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        echo "<li class='nav-item'><a class='nav-link' href='login.php'>  پنل </a></li>";
                        echo "<li class='nav-item'><a class='nav-link' href='logout.php'>خروج</a></li>";
                    } else {
                        echo "<li class='nav-item'><a class='nav-link' href='login.php'>ورود</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="carousel-container">
        <div id="imageCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $featured = $conn->query("SELECT * FROM news WHERE is_featured = 1 AND status = 'approved' ORDER BY date DESC LIMIT 3");
                $first = true;
                if ($featured->num_rows > 0) {
                    while ($row = $featured->fetch_assoc()) {
                        $imagePath = !empty($row['image']) ? (preg_match('/^\/images\//', $row['image']) ? $row['image'] : '/images/' . htmlspecialchars(basename($row['image']))) : '/images/default.jpg';
                        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
                        if (!file_exists($fullPath)) {
                            error_log("تصویر ویژه یافت نشد در $fullPath برای خبر: " . ($row['title'] ?? 'بدون عنوان'));
                            $imagePath = '/images/default.jpg';
                        }
                        echo "<div class='carousel-item" . ($first ? " active" : "") . "' style='background-image: url(" . htmlspecialchars($imagePath) . ");'>
                        </div>";
                        $first = false;
                    }
                } else {
                    echo "<div class='carousel-item active' style='background-image: url(/images/default.jpg);'>
                        <p style='color: #FF5555; text-align: center; padding: 20px;'>هیچ خبر ویژه‌ای یافت نشد.</p>
                    </div>";
                }
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">قبلی</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">بعدی</span>
            </button>
        </div>

        <div id="textCarousel" class="carousel slide text-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $featured->data_seek(0);
                $first = true;
                if ($featured->num_rows > 0) {
                    while ($row = $featured->fetch_assoc()) {
                        echo "<div class='carousel-item" . ($first ? " active" : "") . "'>
                            <h3>" . htmlspecialchars($row['title'] ?? 'بدون عنوان') . "</h3>
                            <p>" . htmlspecialchars(substr($row['content'] ?? '', 0, 100)) . "...</p>
                            <a href='pages/news.php?id=" . ($row['id'] ?? 0) . "' class='btn'>بیشتر</a>
                        </div>";
                        $first = false;
                    }
                } else {
                    echo "<div class='carousel-item active'>
                        <p style='color: #FF5555;'>هیچ خبر ویژه‌ای یافت نشد.</p>
                    </div>";
                }
                $featured->free();
                ?>
            </div>
        </div>
    </div>

    <div class="news-container">
        <?php
        $categories = $conn->query("SELECT id, name FROM categories ORDER BY id");
        if ($categories) {
            while ($row = $categories->fetch_assoc()) {
                $cat_id = $row['id'];
                $cat_name = $row['name'];
                $stmt = $conn->prepare("SELECT n.*, c.name FROM news n JOIN categories c ON n.category_id = c.id WHERE n.category_id = ? AND n.status = 'approved' ORDER BY date DESC LIMIT 3");
                $stmt->bind_param("i", $cat_id);
                $stmt->execute();
                $news = $stmt->get_result();
                if ($news->num_rows > 0) {
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
                                <p class='card-text'>" . htmlspecialchars(substr($row['content'] ?? '', 0, 50)) . "...</p>
                                <a href='pages/news.php?id=" . ($row['id'] ?? 0) . "' class='btn'>بیشتر</a>
                            </div>
                        </div>";
                    }
                    echo "</div>";
                    echo "<div style='text-align: center; margin: 20px 0;'><a href='pages/category.php?cat_id=" . $cat_id . "' class='btn'>ادامه خبرها</a></div>";
                    echo "<div><p></p></div>";
                }
                $stmt->close();
            }
            $categories->free();
        } else {
            error_log("خطا در بارگذاری دسته‌بندی‌ها: " . $conn->error);
            echo "<p style='color: #FF5555; text-align: center;'>خطا در بارگذاری دسته‌بندی‌ها: " . htmlspecialchars($conn->error) . "</p>";
        }
        $conn->close();
        ?>
        <div style="text-align: center; margin-top: 20px;">
            <a href="../index.php" class="btn">بازگشت به خانه</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const imageCarousel = document.querySelector('#imageCarousel');
        const textCarousel = document.querySelector('#textCarousel');
        imageCarousel.addEventListener('slide.bs.carousel', function (e) {
            const bsTextCarousel = bootstrap.Carousel.getOrCreateInstance(textCarousel);
            bsTextCarousel.to(e.to);
        });
        textCarousel.addEventListener('slide.bs.carousel', function (e) {
            const bsImageCarousel = bootstrap.Carousel.getOrCreateInstance(imageCarousel);
            bsImageCarousel.to(e.to);
        });
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => card.style.transform = 'scale(1.05)');
            card.addEventListener('mouseleave', () => card.style.transform = 'scale(1)');
        });
    </script>
</body>

</html>