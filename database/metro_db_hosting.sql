/* =========================================================
   metro_db_hosting.sql: نسخة الاستضافة من قاعدة البيانات
   ---------------------------------------------------------
   الاستضافات المجانية (مثل InfinityFree) ما تسمح بأمر CREATE DATABASE،
   فهذا الملف بدون إنشاء قاعدة بيانات:
   1. أنشئي قاعدة البيانات من لوحة الاستضافة
   2. افتحيها في phpMyAdmin واستوردي هذا الملف
   (نفس جداول وبيانات metro_db.sql بالضبط)
   ========================================================= */

SET NAMES utf8mb4;

DROP TABLE IF EXISTS suggestions;
DROP TABLE IF EXISTS places;
DROP TABLE IF EXISTS station_lines;
DROP TABLE IF EXISTS stations;
DROP TABLE IF EXISTS metro_lines;
DROP TABLE IF EXISTS admins;


/*1- metro_lines table*/
CREATE TABLE metro_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7) NOT NULL
);


/*2- station table*/
CREATE TABLE stations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255)
);


/*3- station_lines table*/
CREATE TABLE station_lines (
    station_id INT NOT NULL,
    line_id INT NOT NULL,
    PRIMARY KEY (station_id, line_id),
    FOREIGN KEY (station_id) REFERENCES stations(id) ON DELETE CASCADE,
    FOREIGN KEY (line_id) REFERENCES metro_lines(id) ON DELETE CASCADE
);


/*4- places table*/
CREATE TABLE places (
    id INT AUTO_INCREMENT PRIMARY KEY,
    station_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    category ENUM ('مول','مقهى','معلم','مطعم','حديقة') NOT NULL,
    description TEXT,
    walk_minutes TINYINT NOT NULL,
    image VARCHAR(255),
    map_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (station_id) REFERENCES stations(id) ON DELETE CASCADE
);


/*5- suggestions table*/
CREATE TABLE suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    station_id INT NOT NULL,
    place_name VARCHAR(100) NOT NULL,
    category ENUM ('مول','مقهى','معلم','مطعم','حديقة') NOT NULL,
    note TEXT,
    visitor_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    status ENUM('new', 'approved', 'rejected') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (station_id) REFERENCES stations(id) ON DELETE CASCADE
);


/*6- admins table*/
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

INSERT INTO metro_lines (name, color) VALUES
('المسار الأزرق', '#0072CE'),
('المسار الأحمر', '#E30613'),
('المسار الأصفر', '#FFC72C');

INSERT INTO stations (name, description, image) VALUES
('المركز المالي (KAFD)', 'محطة تبديل بين الخط الأزرق والأصفر في قلب مركز الملك عبدالله المالي.', 'kafd.jpg'),
('محطة STC', 'محطة تبديل بين الخط الأزرق والأحمر على طريق الملك فهد.', 'stc.jpg'),
('المتحف الوطني', 'قريبة من المتحف الوطني ومركز الملك عبدالعزيز التاريخي.', 'museum.jpg'),
('قصر الحكم', 'في قلب الرياض القديمة، قريبة من الديرة والمصمك.', 'qasr.jpg'),
('جامعة الملك سعود', 'بداية الخط الأحمر عند جامعة الملك سعود.', 'ksu.jpg'),
('التخصصي', 'على شارع التخصصي، وحولها مقاهي ومطاعم كثيرة.', 'takhassusi.jpg'),
('جامعة الأميرة نورة', 'على خط المطار، بجانب جامعة الأميرة نورة بنت عبدالرحمن.', 'pnu.jpg'),
('مطار الملك خالد (صالة 1-2)', 'نهاية الخط الأصفر داخل مطار الملك خالد الدولي.', 'airport.jpg');

INSERT INTO station_lines (station_id, line_id) VALUES
(1, 1), (1, 3),
(2, 1), (2, 2),
(3, 1),
(4, 1),
(5, 2),
(6, 2),
(7, 3),
(8, 3);

INSERT INTO places (station_id, name, category, description, walk_minutes, image) VALUES
(4, 'قصر المصمك', 'معلم', 'حصن تاريخي من الطين شهد استرداد الرياض عام 1902.', 8, 'masmak.jpg'),
(4, 'جامع الإمام تركي بن عبدالله', 'معلم', 'الجامع الكبير في منطقة قصر الحكم.', 3, 'turki-mosque.jpg'),
(4, 'سوق الزل', 'معلم', 'من أقدم أسواق الرياض، فيه البشوت والسجاد والتحف.', 10, 'zal.jpg'),
(3, 'المتحف الوطني السعودي', 'معلم', 'يعرض تاريخ الجزيرة العربية والمملكة عبر قاعات متعددة.', 5, 'national-museum.jpg'),
(3, 'قصر المربع', 'معلم', 'قصر الملك عبدالعزيز التاريخي داخل مركز الملك عبدالعزيز.', 7, 'murabba.jpg'),
(3, 'حدائق مركز الملك عبدالعزيز التاريخي', 'حديقة', 'مساحات خضراء واسعة مناسبة للمشي والجلسات العائلية.', 6, 'historical-park.jpg'),
(5, 'جامعة الملك سعود', 'معلم', 'أقدم جامعة في المملكة، تأسست عام 1957.', 5, 'ksu-campus.jpg');

INSERT INTO suggestions (station_id, place_name, category, note, visitor_name, email) VALUES
(6, 'مقهى تجريبي', 'مقهى', 'مقهى هادئ مناسب للمذاكرة.', 'سارة', 'sara@example.com'),
(1, 'مطعم تجريبي', 'مطعم', 'أكل لذيذ وأسعار مناسبة.', 'نورة', 'noura@example.com');


/* حساب المشرف التجريبي (الطالبة 3)
   اسم المستخدم: admin
   كلمة المرور:  Metro@2026
   كلمة المرور مخزنة مشفرة بـ password_hash() ولا تُخزن كنص عادي */
INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$DSRUW3lIc66Hui0EwGbUhey.WrBwrFMFKgDMG7/E7hlHdnysBl0ha');
