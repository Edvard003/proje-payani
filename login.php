<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ورود</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFF5E1;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #FFFFFF;
            padding: 20px;
            border: 2px solid #FFD700;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .form-control {
            border: 1px solid #FFD700;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 5px;
            width: 100%;
        }
        .btn {
            background-color: #FF0000;
            color: #FFD700;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #CC0000;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="color: #FF0000; text-align: center;">ورود</h2>
        <form action="login_process.php" method="POST">
            <input type="text" name="username" class="form-control" placeholder="نام کاربری" required>
            <input type="password" name="password" class="form-control" placeholder="رمز عبور" required>
            <button type="submit" class="btn">ورود</button>
        </form>
    </div>
</body>
</html>