<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>اخبار ایران</title>
    <!-- CDN Bootstrap CSS -->
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