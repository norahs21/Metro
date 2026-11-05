<?php
/* صفحة دخول المشرف (الطالبة 3)
   نتحقق من اسم المستخدم من جدول admins، ونقارن كلمة المرور بـ password_verify() */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin-functions.php';

start_admin_session();

// إذا هو مسجّل دخول من قبل، نوديه للوحة التحكم مباشرة
if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'الرجاء إدخال اسم المستخدم وكلمة المرور.';
    } else {
        $stmt = $conn->prepare('SELECT id, username, password_hash FROM admins WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // رقم جلسة جديد بعد الدخول (حماية من Session Fixation)
            session_regenerate_id(true);
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['last_activity']  = time();

            set_flash('success', 'أهلًا ' . $admin['username'] . '، تم تسجيل الدخول بنجاح.');
            redirect('dashboard.php');
        }

        // نفس الرسالة للحالتين عشان ما نكشف إذا اسم المستخدم موجود
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>دخول المشرف | حول المحطة</title>
    <link rel="icon" href="../images/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body class="login-page">

    <main class="login-box">
        <button id="theme-toggle" class="theme-btn" type="button" aria-pressed="false">
            <span class="theme-icon-moon"><?php echo icon('moon'); ?></span>
            <span class="theme-icon-sun"><?php echo icon('sun'); ?></span>
            <span class="theme-label">الوضع الليلي</span>
        </button>

        <div class="login-card">
            <a href="../index.php" class="logo">
                <span class="logo-mark"><?php echo icon('train'); ?></span>
                <span class="logo-text">حول المحطة<small>دليل مترو الرياض</small></span>
            </a>
            <h1>دخول المشرف</h1>
            <p class="lead">سجّل دخولك لإدارة الأماكن والاقتراحات.</p>

            <?php
            echo show_flash();
            if (isset($_GET['logout'])) echo alert_box('success', 'تم تسجيل الخروج بنجاح.');
            if (isset($_GET['expired'])) echo alert_box('info', 'انتهت الجلسة بسبب عدم النشاط، سجّل الدخول مرة ثانية.');
            if ($error) echo alert_box('error', $error);
            ?>

            <form id="login-form" class="form" action="login.php" method="post" novalidate>

                <div class="form-group">
                    <label for="username">اسم المستخدم</label>
                    <input type="text" id="username" name="username" dir="ltr" autocomplete="username"
                           required data-label="اسم المستخدم" value="<?php echo e($username); ?>">
                    <span class="error-msg"></span>
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-with-button">
                        <input type="password" id="password" name="password" dir="ltr" autocomplete="current-password"
                               required data-label="كلمة المرور">
                        <button type="button" class="toggle-password" data-target="password" aria-label="إظهار كلمة المرور" aria-pressed="false">
                            <span class="eye-on"><?php echo icon('eye'); ?></span>
                            <span class="eye-off"><?php echo icon('eye-off'); ?></span>
                        </button>
                    </div>
                    <span class="error-msg"></span>
                </div>

                <button type="submit" class="btn"><?php echo icon('lock'); ?> دخول</button>
            </form>
        </div>

        <a href="../index.php" class="back-link"><?php echo icon('back'); ?> الرجوع للموقع</a>
    </main>

</body>
</html>
