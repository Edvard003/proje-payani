<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>اخبار ایران</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
                            include 'includes/db.php';
                            $categories = $conn->query("SELECT id, name FROM categories ORDER BY id");
                            while ($row = $categories->fetch_assoc()) {
                                echo "<li><a class='dropdown-item' href='pages/category.php?cat_id=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                    session_start();
                    if (isset($_SESSION['user_id'])) {
                        echo "<li class='nav-item'><a class='nav-link' href='admin/index.php'>پنل مدیریت</a></li>";
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
                include 'includes/db.php';
                $featured = $conn->query("SELECT * FROM news WHERE is_featured = 1 ORDER BY date DESC LIMIT 3");
                $first = true;
                while ($row = $featured->fetch_assoc()) {
                    $rawImage = $row['image'];
                    $imagePath = (strpos($row['image'], '/images/') === 0 || strpos($row['image'], 'images/') === 0) ? $row['image'] . '?v=1' : '/images/' . htmlspecialchars($row['image']) . '?v=1';
                    echo "<div class='carousel-item" . ($first ? " active" : "") . "' style='background-image: url(" . $imagePath . "); height: 60vh; background-size: cover; background-position: center; border-radius: 10px;'>
                    </div>";
                    $first = false;
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
                while ($row = $featured->fetch_assoc()) {
                    echo "<div class='carousel-item" . ($first ? " active" : "") . "'>
                        <h3>" . htmlspecialchars($row['title']) . "</h3>
                        <p>" . htmlspecialchars(substr($row['content'], 0, 100)) . "...</p>
                        <a href='pages/news.php?id=" . $row['id'] . "' class='btn btn-danger text-warning'>بیشتر</a>
                    </div>";
                    $first = false;
                }
                ?>
            </div>
        </div>
    </div>


    <div class="news-container">
        <?php
        include 'includes/db.php';
        $categories = $conn->query("SELECT id, name FROM categories ORDER BY id");
        while ($row = $categories->fetch_assoc()) {
            $cat_id = $row['id'];
            $cat_name = $row['name'];
            $stmt = $conn->prepare("SELECT n.*, c.name FROM news n JOIN categories c ON n.category_id = c.id WHERE n.category_id = ? AND n.status = 'approved' ORDER BY date DESC LIMIT 3");
            $stmt->bind_param("i", $cat_id);
            $stmt->execute();
            $news = $stmt->get_result();
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
                        onerror=\"this.src='/images/default.jpg'; console.log('خطا در بارگذاری تصویر کارت: ' + this.src + ', مسیر خام: " . addslashes($rawImage) . "');\">
                    <div class='card-body'>
                        <h5 class='card-title'>" . htmlspecialchars($row['title']) . "</h5>
                        <p class='card-text'>" . htmlspecialchars(substr($row['content'], 0, 50)) . "...</p>
                        <a href='pages/news.php?id=" . $row['id'] . "' class='btn'>بیشتر</a>
                    </div>
                  </div>";
                }
                echo "</div>";
                echo "<div style='text-align: center; margin: 20px 0;'><a href='pages/category.php?cat_id=" . $cat_id . "' class='btn'>ادامه خبرها</a></div>";
                echo "<div><p></p></div>";
            }
            $stmt->close();
        }
        ?>
    </div>