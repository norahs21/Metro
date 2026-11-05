<?php
/* الهيدر المشترك للصفحات العامة
   قبل تضمينه: كل صفحة تحدد $pageTitle (وتقدر تحدد $pageDescription) */
$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
if ($currentPage === 'station') {
    $currentPage = 'stations'; // صفحة تفاصيل المحطة تتبع قسم المحطات
}

$navItems = [
    'index'    => 'الرئيسية',
    'stations' => 'المحطات',
    'suggest'  => 'اقترح مكان',
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | حول المحطة</title>
    <meta name="description" content="<?php echo e($pageDescription ?? 'حول المحطة: دليلك للمقاهي والمطاعم والمعالم القريبة من محطات مترو الرياض.'); ?>">
    <link rel="icon" href="images/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- ملف الجافاسكربت في الـ head عشان الوضع الليلي يتطبق قبل ظهور الصفحة -->
    <script src="js/script.js"></script>
</head>
<body>

<a href="#main" class="skip-link">انتقل للمحتوى</a>

<header class="site-header">
    <div class="header-inner">
        <a href="index.php" class="logo">
            <span class="logo-mark"><?php echo icon('train'); ?></span>
            <span class="logo-text">حول المحطة<small>دليل مترو الرياض</small></span>
        </a>

        <nav class="main-nav" aria-label="القائمة الرئيسية">
            <ul>
                <?php foreach ($navItems as $file => $label): ?>
                    <li>
                        <a href="<?php echo $file; ?>.php"
                           <?php if ($currentPage === $file) echo 'class="active" aria-current="page"'; ?>>
                            <?php echo $label; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <button id="theme-toggle" class="theme-btn" type="button" aria-pressed="false">
            <span class="theme-icon-moon"><?php echo icon('moon'); ?></span>
            <span class="theme-icon-sun"><?php echo icon('sun'); ?></span>
            <span class="theme-label">الوضع الليلي</span>
        </button>
    </div>
    <div class="line-stripe" aria-hidden="true"></div>
</header>

<main id="main">
