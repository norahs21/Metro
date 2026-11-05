<?php
$pageTitle = 'المحطات';
include 'includes/header.php';
?>

<section class="page-header">
    <h1>محطات المترو</h1>
    <p>اختر محطة عشان تشوف الأماكن اللي حولها.</p>
</section>

<!-- أزرار الفلترة -->
<div class="filter-bar">
    <button class="filter-btn active" data-line="all">الكل</button>
    <button class="filter-btn" data-line="blue">الخط الأزرق</button>
    <button class="filter-btn" data-line="red">الخط الأحمر</button>
    <button class="filter-btn" data-line="yellow">الخط الأصفر</button>
</div>

<!-- كروت المحطات -->
<section class="cards">

    <article class="card station-card" data-lines="blue yellow">
        <img src="images/kafd.jpg" alt="محطة المركز المالي">
        <div class="card-body">
            <h3>المركز المالي (KAFD)</h3>
            <div class="badges">
                <span class="line-badge line-blue">الأزرق</span>
                <span class="line-badge line-yellow">الأصفر</span>
            </div>
            <a href="station.php?id=1" class="btn-link">عرض الأماكن</a>
        </div>
    </article>

    <article class="card station-card" data-lines="blue red">
        <img src="images/stc.jpg" alt="محطة STC">
        <div class="card-body">
            <h3>محطة STC</h3>
            <div class="badges">
                <span class="line-badge line-blue">الأزرق</span>
                <span class="line-badge line-red">الأحمر</span>
            </div>
            <a href="station.php?id=2" class="btn-link">عرض الأماكن</a>
        </div>
    </article>

    <article class="card station-card" data-lines="blue">
        <img src="images/museum.jpg" alt="محطة المتحف الوطني">
        <div class="card-body">
            <h3>المتحف الوطني</h3>
            <div class="badges">
                <span class="line-badge line-blue">الأزرق</span>
            </div>
            <a href="station.php?id=3" class="btn-link">عرض الأماكن</a>
        </div>
    </article>

    <article class="card station-card" data-lines="blue">
        <img src="images/qasr.jpg" alt="محطة قصر الحكم">
        <div class="card-body">
            <h3>قصر الحكم</h3>
            <div class="badges">
                <span class="line-badge line-blue">الأزرق</span>
            </div>
            <a href="station.php?id=4" class="btn-link">عرض الأماكن</a>
        </div>
    </article>

    <article class="card station-card" data-lines="red">
        <img src="images/ksu.jpg" alt="محطة جامعة الملك سعود">
        <div class="card-body">
            <h3>جامعة الملك سعود</h3>
            <div class="badges">
                <span class="line-badge line-red">الأحمر</span>
            </div>
            <a href="station.php?id=5" class="btn-link">عرض الأماكن</a>
        </div>
    </article>

    <article class="card station-card" data-lines="red">
        <img src="images/takhassusi.jpg" alt="محطة التخصصي">
        <div class="card-body">
            <h3>التخصصي</h3>
            <div class="badges">
                <span class="line-badge line-red">الأحمر</span>
            </div>
            <a href="station.php?id=6" class="btn-link">عرض الأماكن</a>
        </div>
    </article>

    <article class="card station-card" data-lines="yellow">
        <img src="images/pnu.jpg" alt="محطة جامعة الأميرة نورة">
        <div class="card-body">
            <h3>جامعة الأميرة نورة</h3>
            <div class="badges">
                <span class="line-badge line-yellow">الأصفر</span>
            </div>
            <a href="station.php?id=7" class="btn-link">عرض الأماكن</a>
        </div>
    </article>

    <article class="card station-card" data-lines="yellow">
        <img src="images/airport.jpg" alt="محطة مطار الملك خالد">
        <div class="card-body">
            <h3>مطار الملك خالد (صالة 1-2)</h3>
            <div class="badges">
                <span class="line-badge line-yellow">الأصفر</span>
            </div>
            <a href="station.php?id=8" class="btn-link">عرض الأماكن</a>
        </div>
    </article>

</section>

<?php include 'includes/footer.php'; ?>
