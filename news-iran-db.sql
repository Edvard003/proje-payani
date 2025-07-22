CREATE DATABASE news_iran_db CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci;
USE news_iran_db;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    role ENUM('admin', 'writer') DEFAULT 'writer',
    full_name VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci
);
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    description TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci
);
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    content TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    image VARCHAR(100),
    category_id INT,
    author_id INT,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_featured BOOLEAN DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT,
    user_id INT,
    comment TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
INSERT INTO users (username, password, role, full_name) VALUES
('admin', '123', 'admin', 'مدیر سایت ایران'),
('writer1', '123', 'writer', 'نویسنده اول'),
('writer2', '123', 'writer', 'نویسنده دوم');
INSERT INTO categories (name, description) VALUES
('سیاسی', 'اخبار مربوط به سیاست و دولت ایران'),
('ورزشی', 'اخبار ورزشی ایران و جهان'),
('اقتصادی', 'اخبار اقتصادی و بازار ایران');
INSERT INTO news (title, content, image, category_id, author_id, status, is_featured) VALUES
('جلسه مهم مجلس ایران', 'امروز جلسه‌ای با حضور نمایندگان برای بررسی بودجه برگزار شد...', 'images/images.jpg', 1, 2, 'approved', 1),
('انتخابات ریاست‌جمهوری 1404', 'ثبت‌نام کاندیداها برای انتخابات آتی آغاز شد...', 'images/images2.jpg', 1, 2, 'approved', 0),
('دیپلماسی ایران و روسیه', 'دیدار مقامات ایرانی و روس برای همکاری‌های جدید...', 'images/images3.jpg', 1, 2, 'approved', 0),
('تغییرات در کابینه دولت', 'وزیر جدید اقتصاد معرفی شد...', 'images/images4.jpg', 1, 2, 'approved', 0),
('قانون جدید مالیاتی', 'مجلس قانون جدیدی برای مالیات بر درآمد تصویب کرد...', 'images/images5.jpg', 1, 2, 'approved', 0),
('اعتراضات کارگری در تهران', 'کارگران کارخانه‌ای در تهران دست به اعتراض زدند...', 'images/images6.jpg', 1, 2, 'approved', 0),
('سیاست خارجی ایران', 'اظهارات جدید وزیر خارجه درباره روابط بین‌الملل...', 'images/images7.jpg', 1, 2, 'approved', 0),
('بازگشایی سفارتخانه‌ها', 'ایران و چند کشور توافق به بازگشایی سفارت کردند...', 'images/images8.jpg', 1, 2, 'approved', 0),
('جلسه سران قوا', 'سران سه قوه درباره مسائل ملی دیدار کردند...', 'images/images9.jpg', 1, 2, 'approved', 0),
('اصلاح قانون انتخابات', 'پیشنهادات جدیدی برای اصلاح قانون انتخابات مطرح شد...', 'images/images10.jpg', 1, 2, 'approved', 0),
('پیروزی تیم ملی فوتبال', 'تیم ملی ایران در بازی دوستانه 2-0 پیروز شد...', 'images/images11.jpg', 2, 3, 'approved', 1),
('لیگ برتر ایران', 'استقلال و پرسپولیس به تساوی رسیدند...', 'images/images22.jpg', 2, 3, 'approved', 0),
('ورزشکاران المپیک 2024', 'اسامی ورزشکاران ایرانی برای المپیک اعلام شد...', 'images/images33.jpg', 2, 3, 'approved', 0),
('موفقیت کشتی‌گیران', 'کشتی‌گیران ایران مدال‌های جدیدی کسب کردند...', 'images/images44.jpg', 2, 3, 'approved', 0),
('بازی‌های آسیایی', 'ایران در رنکینگ آسیا صعود کرد...', 'images/images55.jpg', 2, 3, 'approved', 0),
('تیم ملی والیبال', 'پیروزی در برابر ژاپن با نتیجه 3-1...', 'images/images66.jpg', 2, 3, 'approved', 0),
('ورزش بانوان', 'موفقیت تیم ملی بسکتبال بانوان...', 'images/images77.jpg', 2, 3, 'approved', 0),
('فوتبال پایه', 'تیم امید ایران آماده مسابقات شد...', 'images/images88.jpg', 2, 3, 'approved', 0),
('مسابقات دوومیدانی', 'رکوردشکنی دونده ایرانی...', 'images/images99.jpg', 2, 3, 'approved', 0),
('جایزه بهترین مربی', 'مربی تیم ملی ایران جایزه گرفت...', 'images/images1010.jpg', 2, 3, 'approved', 0),
('افزایش قیمت دلار', 'نرخ دلار در بازار امروز به 60 هزار تومان رسید...', 'images/images111.jpg', 3, 3, 'approved', 1),
('تورم در ایران', 'نرخ تورم به 40 درصد رسید...', 'images/images222.jpg', 3, 3, 'approved', 0),
('گشایش اقتصادی', 'دولت برنامه‌های جدید اقتصادی اعلام کرد...', 'images/images333.jpg', 3, 3, 'approved', 0),
('قیمت طلا افزایش یافت', 'طلا در بازار تهران گران‌تر شد...', 'images/images444.jpg', 3, 3, 'approved', 0),
('صادرات نفت ایران', 'افزایش صادرات نفت به چین...', 'images/images555.jpg', 3, 3, 'approved', 0),
('بازار مسکن', 'قیمت مسکن در تهران رکورد زد...', 'images/images666.jpg', 3, 3, 'approved', 0),
('یارانه جدید', 'دولت یارانه جدیدی برای اقشار کم‌درآمد اعلام کرد...', 'images/images777.jpg', 3, 3, 'approved', 0),
('تولید خودرو', 'افزایش تولید خودروهای داخلی...', 'images/images888.jpg', 3, 3, 'approved', 0),
('بورس تهران', 'شاخص بورس امروز رشد کرد...', 'images/images999.jpg', 3, 3, 'approved', 0),
('واردات کالاهای اساسی', 'واردات گندم به ایران افزایش یافت...', 'images/images101010.jpg', 3, 3, 'approved', 0);
INSERT INTO comments (news_id, user_id, comment) VALUES
(1, 2, 'نظر جالبی بود، ممنون از اطلاعات!'),
(2, 3, 'مطلب خوبی بود، منتظر اخبار بیشتر هستم.'),
(3, 2, 'اطلاعات دقیق و مفیدی ارائه شد.');