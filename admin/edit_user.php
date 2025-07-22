<?php include '../includes/db.php';
session_start();
if ($_SESSION['role'] != 'admin')
    header("Location: ../login.php"); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>ویرایش کاربر</title>
    <style>
        body {
            font-family: Arial;
            background: #FFF5E1;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 500px;
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
        $result = $conn->query("SELECT * FROM users WHERE id=$id");
        $row = $result->fetch_assoc();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = $_POST['full_name'];
            $role = $_POST['role'];
            $conn->query("UPDATE users SET full_name='$full_name', role='$role' WHERE id=$id");
            header("Location: index.php?page=users");
        }
        ?>
        <form method="POST">
            <input type="text" name="full_name" class="form-control" value="<?php echo $row['full_name']; ?>" required>
            <select name="role" class="form-control">
                <option value="admin" <?php echo $row['role'] == 'admin' ? 'selected' : ''; ?>>ادمین</option>
                <option value="writer" <?php echo $row['role'] == 'writer' ? 'selected' : ''; ?>>نویسنده</option>
            </select>
            <button type="submit" class="btn">ذخیره</button>
        </form>
    </div>
</body>

</html>