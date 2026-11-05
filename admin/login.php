<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول المشرف | حول المحطة</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-page">

    <main class="login-box">
        <a href="../index.php" class="logo">حول المحطة</a>
        <h1>دخول المشرف</h1>

        <form id="login-form" class="form" action="login.php" method="post" novalidate>

            <div class="form-group">
                <label for="username">اسم المستخدم</label>
                <input type="text" id="username" name="username" dir="ltr">
                <span class="error-msg"></span>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" dir="ltr">
                <span class="error-msg"></span>
            </div>

            <button type="submit" class="btn">دخول</button>

        </form>

        <a href="../index.php" class="back-link">&rarr; الرجوع للموقع</a>
    </main>

<script src="../js/script.js"></script>
</body>
</html>
