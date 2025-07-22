<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>جستجو</title>
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
            background: #FFFFFF;
            border: 2px solid #FFD700;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card {
            width: 100%;
            margin: 10px 0;
            background: #FF6347;
            border: 2px solid #FFD700;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .card-body {
            padding: 15px;
            color: #FFD700;
        }
        .btn {
            background-color: #FF0000;
            color: #FFD700;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #CC0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $search = $_GET['q'] ?? '';
        $result = $conn->query("SELECT n.*, c.name FROM news n JOIN categories c ON n.category_id = c.id WHERE n.title LIKE '%$search%'");
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card'>
                    <img src='" . $row['image'] . "' alt='" . $row['title'] . "'>
                    <div class='card-body'>
                        <h5>" . $row['title'] . "</h5>
                        <p>" . substr($row['content'], 0, 50) . "...</p>
                        <a href='/news_project/pages/news.php?id=" . $row['id'] . "' class='btn'>بیشتر</a>
                    </div>
                  </div>";
        }
        ?>
    </div>
</body>
</html>