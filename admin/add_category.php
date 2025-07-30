<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    if ($stmt->execute()) {
        header("Location: index.php?page=categories&success=دسته‌بندی با موفقیت اضافه شد");
    } else {
        echo "<p style='color: #FF5555; text-align: center;'>خطا در افزودن دسته‌بندی</p>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>ایجاد دسته‌بندی</title>
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
            max-width: 600px;
            margin: 20px auto;
            background: #1A1A1A;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .form-control {
            border: 1px solid #FF0000;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            background: #333333;
            color: #FFFFFF;
        }

        .form-control:focus {
            border-color: #FFD700;
            box-shadow: 0 0 5px rgba(255, 215, 0, 0.5);
        }

        .btn {
            background: #FF0000;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background: #CC0000;
            transform: scale(1.05);
        }

        .nav-link {
            color: #FFD700;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        .nav-link:hover {
            color: #FFFFFF;
        }

        h2 {
            color: #FF0000;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>ایجاد دسته‌بندی جدید</h2>
        <div style="margin-bottom: 20px; text-align: center;">
            <a href="index.php?page=categories" class="nav-link">بازگشت به پنل مدیریت</a>
        </div>
        <form method="POST" action="add_category.php">
            <input type="text" name="name" class="form-control" placeholder="نام دسته‌بندی" required>
            <textarea name="description" class="form-control" placeholder="توضیحات" rows="4"></textarea>
            <button type="submit" class="btn">ایجاد دسته‌بندی</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>