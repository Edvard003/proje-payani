<?php
include '../includes/db.php';
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'writer' || !isset($_SESSION['user_id'])) {
    header("Location: ../login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
$author_id = (int)$_SESSION['user_id'];

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$edit_mode = false;
$edit_news = null;
$error = '';
$success = '';

if (isset($_GET['edit_id'])) {
    $edit_id = filter_input(INPUT_GET, 'edit_id', FILTER_VALIDATE_INT);
    if ($edit_id !== false && $edit_id > 0) {
        $stmt = $conn->prepare("SELECT id, title, content, image, category_id FROM news WHERE id = ? AND author_id = ?");
        if ($stmt === false) {
            error_log("خطا در آماده‌سازی کوئری SELECT news: " . $conn->error);
            header("Location: ../index.php?error=خطا در بارگذاری خبر");
            exit();
        }
        $stmt->bind_param("ii", $edit_id, $author_id);
        $stmt->execute();
        $edit_news = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($edit_news) {
            $edit_mode = true;
        } else {
            header("Location: ../index.php?error=شما اجازه ویرایش این خبر را ندارید");
            exit();
        }
    } else {
        header("Location: ../index.php?error=شناسه خبر نامعتبر است");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'خطای امنیتی CSRF';
    } else {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $news_id = filter_input(INPUT_POST, 'news_id', FILTER_VALIDATE_INT);

        if (empty($title) || empty($content) || $category_id === false || $category_id <= 0) {
            $error = 'لطفاً همه فیلدها را به‌درستی پر کنید';
        } else {
            $stmt = $conn->prepare("SELECT id FROM categories WHERE id = ?");
            if ($stmt === false) {
                error_log("خطا در آماده‌سازی کوئری SELECT category: " . $conn->error);
                $error = 'خطا در بررسی دسته‌بندی';
            } else {
                $stmt->bind_param("i", $category_id);
                $stmt->execute();
                if ($stmt->get_result()->num_rows === 0) {
                    $error = 'دسته‌بندی نامعتبر است';
                }
                $stmt->close();
            }

            $imagePath = $edit_mode ? $edit_news['image'] : '/images/default.jpg';
            if (!$error && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . '../images/';
                error_log("DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT']);
                error_log("Target Directory: $target_dir");
                if (!is_dir($target_dir)) {
                    if (!mkdir($target_dir, 0755, true)) {
                        error_log("نمی‌توان دایرکتوری $target_dir را ایجاد کرد");
                        $error = 'خطا در ایجاد دایرکتوری تصاویر';
                    }
                }
                if (!$error && !is_writable($target_dir)) {
                    error_log("دایرکتوری $target_dir قابل نوشتن نیست");
                    $error = 'خطا در دسترسی به دایرکتوری تصاویر';
                } else {
                    $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                    $max_size = 2 * 1024 * 1024; // 2MB
                    $mime_types = ['image/jpeg', 'image/png', 'image/gif'];

                    if (!in_array($imageFileType, $allowed_types) || !in_array($_FILES['image']['type'], $mime_types) || $_FILES['image']['size'] > $max_size) {
                        $error = 'فرمت یا اندازه تصویر نامعتبر است';
                    } else {
                        $new_filename = uniqid() . '.' . $imageFileType;
                        $target_file = $target_dir . $new_filename;
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                            $imagePath = '/images/' . $new_filename;
                            error_log("تصویر با موفقیت آپلود شد: $imagePath");
                            if ($edit_mode && $edit_news['image'] !== '/images/default.jpg' && file_exists(__DIR__ . '../' . $edit_news['image'])) {
                                unlink(__DIR__ . '../' . $edit_news['image']);
                            }
                        } else {
                            error_log("خطا در آپلود تصویر: " . $_FILES['image']['error'] . ", مسیر: $target_file");
                            $error = 'خطا در آپلود تصویر';
                        }
                    }
                }
            } elseif (!$edit_mode && (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK)) {
                $error = 'لطفاً یک تصویر انتخاب کنید';
            }

            if (!$error) {
                if ($edit_mode && $news_id) {
                    $stmt = $conn->prepare("UPDATE news SET title = ?, content = ?, image = ?, category_id = ?, status = 'pending' WHERE id = ? AND author_id = ?");
                    if ($stmt === false) {
                        error_log("خطا در آماده‌سازی کوئری UPDATE news: " . $conn->error);
                        $error = 'خطا در ویرایش خبر';
                    } else {
                        $stmt->bind_param("sssiii", $title, $content, $imagePath, $category_id, $news_id, $author_id);
                        if ($stmt->execute()) {
                            $success = 'خبر با موفقیت ویرایش شد';
                            header("Location: ../index.php?success=" . urlencode($success));
                            exit();
                        } else {
                            error_log("خطا در اجرای کوئری UPDATE: " . $stmt->error);
                            $error = 'خطا در ویرایش خبر';
                        }
                        $stmt->close();
                    }
                } else {
                    $stmt = $conn->prepare("INSERT INTO news (title, content, image, category_id, author_id, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                    if ($stmt === false) {
                        error_log("خطا در آماده‌سازی کوئری INSERT news: " . $conn->error);
                        $error = 'خطا در ثبت خبر';
                    } else {
                        $stmt->bind_param("sssii", $title, $content, $imagePath, $category_id, $author_id);
                        if ($stmt->execute()) {
                            $success = 'خبر با موفقیت ثبت شد';
                            header("Location: ../index.php?success=" . urlencode($success));
                            exit();
                        } else {
                            error_log("خطا در اجرای کوئری INSERT: " . $stmt->error);
                            $error = 'خطا در ثبت خبر';
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'ویرایش خبر' : 'ایجاد خبر جدید'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.0/dist/css/vazirmatn.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Vazirmatn', Arial, sans-serif;
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
            border: 2px solid #FF0000;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        .form-control {
            border: 1px solid #FF0000;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            background: #222222;
            color: #FFD700;
        }
        .form-control:focus {
            border-color: #FFD700;
            box-shadow: 0 0 5px rgba(255, 215, 0, 0.5);
        }
        .form-control::placeholder {
            color: #FFD700;
            opacity: 0.7;
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
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            display: none;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo $edit_mode ? 'ویرایش خبر' : 'ایجاد خبر جدید'; ?></h2>
        <div style="margin-bottom: 20px; text-align: center;">
            <a href="../index.php" class="nav-link">بازگشت به پنل نویسنده</a>
            <a href="../add_category.php" class="nav-link">ایجاد دسته‌بندی</a>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="add_news.php<?php echo $edit_mode ? '?edit_id=' . $edit_news['id'] : ''; ?>" enctype="multipart/form-data">
            <input type="hidden" name="news_id" value="<?php echo $edit_mode ? $edit_news['id'] : ''; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="title" class="form-control" placeholder="عنوان خبر" value="<?php echo $edit_mode ? htmlspecialchars($edit_news['title']) : ''; ?>" required>
            <textarea name="content" class="form-control" placeholder="محتوا" rows="6" required><?php echo $edit_mode ? htmlspecialchars($edit_news['content']) : ''; ?></textarea>
            <input type="file" name="image" id="imageInput" class="form-control" accept="image/jpeg,image/png,image/gif" <?php echo $edit_mode ? '' : 'required'; ?>>
            <?php if ($edit_mode && !empty($edit_news['image']) && $edit_news['image'] !== '/images/default.jpg'): ?>
                <img src="<?php echo htmlspecialchars($edit_news['image']); ?>" alt="پیش‌نمایش تصویر" class="image-preview" style="display: block;">
            <?php endif; ?>
            <img id="imagePreview" class="image-preview" alt="پیش‌نمایش تصویر">
            <select name="category_id" class="form-control" required>
                <option value="">انتخاب دسته‌بندی</option>
                <?php
                $stmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name");
                if ($stmt) {
                    $stmt->execute();
                    $categories = $stmt->get_result();
                    while ($cat = $categories->fetch_assoc()) {
                        $selected = ($edit_mode && $edit_news['category_id'] == $cat['id']) ? 'selected' : '';
                        echo "<option value='{$cat['id']}' {$selected}>" . htmlspecialchars($cat['name']) . "</option>";
                    }
                    $stmt->close();
                } else {
                    error_log("خطا در آماده‌سازی کوئری SELECT categories: " . $conn->error);
                    $error = 'خطا در بارگذاری دسته‌بندی‌ها';
                }
                ?>
            </select>
            <button type="submit" class="btn"><?php echo $edit_mode ? 'ویرایش خبر' : 'ایجاد خبر'; ?></button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <a href="../index.php" class="btn">بازگشت به خانه</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>