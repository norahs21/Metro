<?php
/* الهيدر المشترك لصفحات المشرف (بعد auth.php) */
$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title><?php echo e($pageTitle); ?> | لوحة التحكم</title>
    <link rel="icon" href="../images/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body class="admin">

<a href="#main" class="skip-link">انتقل للمحتوى</a>

<header class="site-header">
    <div class="header-inner">
        <a href="dashboard.php" class="logo">
            <span class="logo-mark"><?php echo icon('train'); ?></span>
            <span class="logo-text">حول المحطة <span class="admin-tag">المشرف</span><small>لوحة التحكم</small></span>
        </a>

        <nav class="main-nav" aria-label="قائمة المشرف">
            <ul>
                <li><a href="dashboard.php" <?php if ($currentPage === 'dashboard') echo 'class="active" aria-current="page"'; ?>>لوحة التحكم</a></li>
                <li><a href="add-place.php" <?php if ($currentPage === 'add-place') echo 'class="active" aria-current="page"'; ?>>إضافة مكان</a></li>
                <li><a href="../index.php" target="_blank" rel="noopener">عرض الموقع</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <span class="user-chip"><?php echo icon('user'); ?><?php echo e($_SESSION['admin_username']); ?></span>
            <button id="theme-toggle" class="theme-btn" type="button" aria-pressed="false" aria-label="تبديل الوضع الليلي">
                <span class="theme-icon-moon"><?php echo icon('moon'); ?></span>
                <span class="theme-icon-sun"><?php echo icon('sun'); ?></span>
            </button>
            <a href="logout.php" class="logout-btn"><?php echo icon('logout'); ?><span>تسجيل الخروج</span></a>
        </div>
    </div>
    <div class="line-stripe" aria-hidden="true"></div>
</header>

<main id="main" class="admin-main">

<?php echo show_flash(); ?>
