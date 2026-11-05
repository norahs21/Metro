<?php
/* تسجيل الخروج (الطالبة 3): نمسح بيانات الجلسة ونرجع لصفحة الدخول */
require_once __DIR__ . '/../includes/admin-functions.php';

start_admin_session();

$_SESSION = [];
session_destroy();

header('Location: login.php?logout=1');
exit;
