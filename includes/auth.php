<?php
/* =========================================================
   auth.php: حماية صفحات المشرف (الطالبة 3)
   أول سطر في كل صفحة محمية: require_once '../includes/auth.php';
   إذا المشرف ما سجّل دخول، يتحول لصفحة الدخول ولا يشوف شي
   ========================================================= */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/admin-functions.php';

start_admin_session();

// 1. ما فيه تسجيل دخول
if (empty($_SESSION['admin_id'])) {
    set_flash('error', 'سجّل الدخول أولًا للوصول للوحة التحكم.');
    redirect('login.php');
}

// 2. انتهت الجلسة بسبب الخمول
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    redirect('login.php?expired=1');
}

$_SESSION['last_activity'] = time();
