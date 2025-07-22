<?php
$conn = new mysqli("news-iran-db", "root", "SEGmxDBcaYPfYFzvuIWJD5WC", "focused_mayer");
if ($conn->connect_error) {
    die("اتصال به دیتابیس ناموفق: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>