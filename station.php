<?php
$pageTitle = 'قصر الحكم';
include 'includes/header.php';
?>

<a href="stations.php" class="back-link">&larr; الرجوع للمحطات</a>

<!-- معلومات المحطة -->
<section class="station-info">
    <img src="images/qasr.jpg" alt="محطة قصر الحكم" class="station-img">
    <div class="station-text">
        <h1>قصر الحكم</h1>
        <div class="badges">
            <span class="line-badge line-blue">الأزرق</span>
        </div>
        <p>في قلب الرياض القديمة، قريبة من الديرة والمصمك.</p>
    </div>
</section>

<!-- أزرار فلترة الأماكن -->
<h2>الأماكن القريبة</h2>
<div class="filter-bar">
    <button class="filter-btn active" data-category="all">الكل</button>
    <button class="filter-btn" data-category="مقهى">مقهى</button>
    <button class="filter-btn" data-category="مطعم">مطعم</button>
    <button class="filter-btn" data-category="معلم">معلم</button>
    <button class="filter-btn" data-category="حديقة">حديقة</button>
    <button class="filter-btn" data-category="مول">مول</button>
</div>

<!-- كروت الأماكن -->
<section class="cards">

    <article class="card place-card" data-category="معلم">
        <img src="images/masmak.jpg" alt="قصر المصمك">
        <div class="card-body">
            <span class="category-tag">معلم</span>
            <h3>قصر المصمك</h3>
            <p>حصن تاريخي من الطين شهد استرداد الرياض عام 1902.</p>
            <p class="walk-time">8 دقائق مشيًا</p>
            <a href="#" class="btn-link" target="_blank">عرض على الخريطة</a>
        </div>
    </article>

    <article class="card place-card" data-category="معلم">
        <img src="images/turki-mosque.jpg" alt="جامع الإمام تركي بن عبدالله">
        <div class="card-body">
            <span class="category-tag">معلم</span>
            <h3>جامع الإمام تركي بن عبدالله</h3>
            <p>الجامع الكبير في منطقة قصر الحكم.</p>
            <p class="walk-time">3 دقائق مشيًا</p>
            <a href="#" class="btn-link" target="_blank">عرض على الخريطة</a>
        </div>
    </article>

    <article class="card place-card" data-category="معلم">
        <img src="images/zal.jpg" alt="سوق الزل">
        <div class="card-body">
            <span class="category-tag">معلم</span>
            <h3>سوق الزل</h3>
            <p>من أقدم أسواق الرياض، فيه البشوت والسجاد والتحف.</p>
            <p class="walk-time">10 دقائق مشيًا</p>
            <a href="#" class="btn-link" target="_blank">عرض على الخريطة</a>
        </div>
    </article>

</section>

<?php include 'includes/footer.php'; ?>
