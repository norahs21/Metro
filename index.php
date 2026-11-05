<?php
$pageTitle = 'الرئيسية';
include 'includes/header.php';
?>

<!-- 1. الواجهة -->
<section class="hero">
    <h1>اكتشف وش حول محطتك</h1>
    <p>دليلك للمقاهي والمطاعم والمعالم اللي تقدر توصلها مشيًا من محطات مترو الرياض.</p>
    <a href="stations.php" class="btn">استكشف المحطات</a>
</section>

<!-- 2. خطوط المترو -->
<section class="lines">
    <h2>خطوط المترو</h2>
    <div class="lines-list">
        <a href="stations.php" class="line-badge line-blue">الخط الأزرق</a>
        <a href="stations.php" class="line-badge line-red">الخط الأحمر</a>
        <a href="stations.php" class="line-badge line-yellow">الخط الأصفر</a>
    </div>
</section>

<!-- 3. محطات مميزة -->
<section class="featured">
    <h2>محطات مميزة</h2>
    <div class="cards">

        <article class="card">
            <img src="images/qasr.jpg" alt="محطة قصر الحكم">
            <div class="card-body">
                <h3>قصر الحكم</h3>
                <p>في قلب الرياض القديمة، قريبة من الديرة والمصمك.</p>
                <a href="station.php?id=4" class="btn-link">عرض الأماكن</a>
            </div>
        </article>

        <article class="card">
            <img src="images/museum.jpg" alt="محطة المتحف الوطني">
            <div class="card-body">
                <h3>المتحف الوطني</h3>
                <p>قريبة من المتحف الوطني ومركز الملك عبدالعزيز التاريخي.</p>
                <a href="station.php?id=3" class="btn-link">عرض الأماكن</a>
            </div>
        </article>

        <article class="card">
            <img src="images/kafd.jpg" alt="محطة المركز المالي">
            <div class="card-body">
                <h3>المركز المالي (KAFD)</h3>
                <p>محطة تبديل بين الخط الأزرق والأصفر في قلب مركز الملك عبدالله المالي.</p>
                <a href="station.php?id=1" class="btn-link">عرض الأماكن</a>
            </div>
        </article>

    </div>
</section>

<!-- 4. اقترح مكان -->
<section class="cta">
    <h2>تعرف مكان حلو حول محطة؟</h2>
    <p>شاركنا اقتراحك، ونضيفه بعد المراجعة.</p>
    <a href="suggest.php" class="btn">اقترح مكان</a>
</section>

<?php include 'includes/footer.php'; ?>
