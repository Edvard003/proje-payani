<?php include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'admin')
    header("Location: ../login.php"); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>ویرایش خبر</title>
    <style>
        body {
            font-family: Arial;
            background: #FFF5E1;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #FFFFFF;
            padding: 20px;
            border: 2px solid #FFD700;
            border-radius: 10px;
        }

        .form-control {
            border: 1px solid #FFD700;
            border-radius: 5px;
            padding: 5px;
            width: 100%;
            margin-bottom: 10px;
        }

        .btn {
            background: #FF0000;
            color: #FFD700;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #CC0000;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        $id = $_GET['id'];
        $result = $conn->query("SELECT * FROM news WHERE id=$id");
        $row = $result->fetch_assoc();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $image = $_POST['image'];
            $category_id = $_POST['category_id'];
            $status = $_POST['status'];
            $conn->query("UPDATE news SET title='$title', content='$content', image='$image', category_id=$category_id, status='$status' WHERE id=$id");
            header("Location: index.php?page=news");
        }
        ?>
        <form method="POST">
            <input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>" required>
            <textarea name="content" class="form-control" required><?php echo $row['content']; ?></textarea>
            <input type="text" name="image" class="form-control" value="<?php echo $row['image']; ?>"
                placeholder="آدرس تصویر">
            <select name="category_id" class="form-control">
                <?php
                $cats = $conn->query("SELECT * FROM categories");
                while ($cat = $cats->fetch_assoc()) {
                    echo "<option value='" . $cat['id'] . "' " . ($row['category_id'] == $cat['id'] ? 'selected' : '') . ">" . $cat['name'] . "</option>";
                }
                ?>
            </select>
            <select name="status" class="form-control">
                <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>در انتظار</option>
                <option value="approved" <?php echo $row['status'] == 'approved' ? 'selected' : ''; ?>>تأیید شده</option>
                <option value="rejected" <?php echo $row['status'] == 'rejected' ? 'selected' : ''; ?>>رد شده</option>
            </select>
            <button type="submit" class="btn">ذخیره</button>
        </form>
    </div>
</body>

</html>