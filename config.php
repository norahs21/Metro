<?php
/* =========================================================
   config.php: الاتصال بقاعدة البيانات (الطالبة 2)
   ---------------------------------------------------------
   على XAMPP تشتغل القيم الافتراضية بدون تعديل.
   عند الرفع على الاستضافة: غيّري القيم الأربع فقط
   (تجدينها في لوحة الاستضافة تحت MySQL Databases).
   ========================================================= */

define('DB_HOST', 'localhost');   // مثال للاستضافة: sql123.infinityfree.com
define('DB_USER', 'root');        // مثال: if0_12345678
define('DB_PASS', '');            // كلمة مرور قاعدة البيانات
define('DB_NAME', 'metro_db');    // مثال: if0_12345678_metro_db

// نخلي mysqli يرمي Exception عند أي خطأ، عشان نقدر نلتقطه بـ try/catch
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4'); // ضروري عشان النصوص العربية تظهر صح
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><title>خطأ في الاتصال</title></head>'
        . '<body style="font-family:Tahoma,sans-serif;text-align:center;padding:60px 16px;background:#F5F6F2;color:#15201B">'
        . '<h1 style="font-size:1.4rem">تعذّر الاتصال بقاعدة البيانات</h1>'
        . '<p>تأكدي من تشغيل MySQL ومن استيراد ملف metro_db.sql ومن بيانات الاتصال في config.php</p>'
        . '</body></html>');
}

// الدوال المساعدة المشتركة بين كل الصفحات
require_once __DIR__ . '/includes/functions.php';
