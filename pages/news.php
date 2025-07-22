<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>جزئیات خبر</title>
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
            max-width: 800px;
            margin: 20px auto;
            background: #1A1A1A;
            border: 2px solid #FFD700;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        h2 {
            color: #FFD700;
            text-align: center;
            font-size: 2em;
            margin-bottom: 10px;
        }

        img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 2px solid #FFD700;
        }

        .meta-info {
            color: #DDD;
            font-size: 0.9em;
            margin-bottom: 10px;
            text-align: center;
        }

        .content {
            color: #DDD;
            line-height: 1.6;
            font-size: 1em;
        }

        h4 {
            color: #FFD700;
            margin-top: 20px;
        }

        .comment {
            background: #FF6347;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            color: #FFD700;
        }

        .form-control {
            border: 1px solid #FFD700;
            border-radius: 5px;
            margin-bottom: 10px;
            background: #333;
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
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $conn->prepare("SELECT n.*, c.name AS category_name, u.full_name AS author_name FROM news n JOIN categories c ON n.category_id = c.id JOIN users u ON n.author_id = u.id WHERE n.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                $rawImage = $row['image'];
                $cleanImage = preg_replace('/^\/images\//', '', $rawImage);
                $imagePath = !empty($cleanImage) ? '/images/' . htmlspecialchars(basename($cleanImage)) : '/images/default.jpg';
                $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
                if (!file_exists($fullPath)) {
                    error_log("تصویر یافت نشد در $fullPath برای خبر: " . $row['title'] . ", مسیر خام: " . $rawImage);
                    $imagePath = '/images/default.jpg';
                }
                echo "<img src='" . $imagePath . "' class='img-fluid mb-3' onerror=\"this.src='/images/default.jpg'; console.log('خطا در بارگذاری تصویر خبر: ' + this.src + ', مسیر خام: " . addslashes($rawImage) . "');\">";
                $author = htmlspecialchars($row['author_name'] ?? 'نویسنده نامشخص');
                $date = date('Y-m-d H:i', strtotime($row['date'])); 
                echo "<div class='meta-info'>نویسنده: $author | تاریخ: $date</div>";
                echo "<div class='content'>" . htmlspecialchars($row['content']) . "</div>";
                echo "<h4>نظرات</h4>";
                echo "<div>";
                $comment_result = $conn->query("SELECT c.comment, u.full_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.news_id = $id");
                while ($comment = $comment_result->fetch_assoc()) {
                    echo "<div class='comment'>" . htmlspecialchars($comment['comment']) . " - توسط: " . htmlspecialchars($comment['full_name']) . "</div>";
                }
                echo "</div>";
                if (isset($_SESSION['user_id'])) {
                    echo "<form method='POST' action='comment.php?news_id=$id'>
                            <textarea name='comment' class='form-control' placeholder='نظر خود را بنویسید...' required></textarea>
                            <button type='submit' class='btn'>ارسال نظر</button>
                          </form>";
                } else {
                    echo "<p style='color: #FF0000;'>برای ارسال نظر باید <a href='login.php'>وارد شوید</a>.</p>";
                }
            } else {
                echo "<p style='color: #FF0000; text-align: center;'>خبر یافت نشد!</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: #FF0000; text-align: center;'>شناسه خبر نامعتبر است!</p>";
        }
        ?>
        <a href="../index.php" class="btn" style="margin-top: 20px;">بازگشت به خانه</a>
    </div>
</body>

</html>