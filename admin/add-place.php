<?php
$pageTitle = 'إضافة مكان';
include '../includes/admin-header.php';
?>

<a href="dashboard.php" class="back-link">&rarr; الرجوع للوحة التحكم</a>

<section class="form-section">
    <h1>إضافة مكان جديد</h1>
    <p class="muted">عبّي بيانات المكان القريب من المحطة.</p>
    <br>

    <form id="place-form" class="form" action="add-place.php" method="post" novalidate>

        <div class="form-group">
            <label for="station_id">المحطة</label>
            <select id="station_id" name="station_id">
                <option value="">اختر المحطة</option>
                <option value="1">المركز المالي (KAFD)</option>
                <option value="2">محطة STC</option>
                <option value="3">المتحف الوطني</option>
                <option value="4">قصر الحكم</option>
                <option value="5">جامعة الملك سعود</option>
                <option value="6">التخصصي</option>
                <option value="7">جامعة الأميرة نورة</option>
                <option value="8">مطار الملك خالد (صالة 1-2)</option>
            </select>
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="name">اسم المكان</label>
            <input type="text" id="name" name="name">
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="category">النوع</label>
            <select id="category" name="category">
                <option value="">اختر النوع</option>
                <option value="مقهى">مقهى</option>
                <option value="مطعم">مطعم</option>
                <option value="معلم">معلم</option>
                <option value="حديقة">حديقة</option>
                <option value="مول">مول</option>
            </select>
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="description">الوصف</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="walk_minutes">المسافة مشيًا (بالدقائق)</label>
            <input type="number" id="walk_minutes" name="walk_minutes" min="1" max="60">
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="image">اسم ملف الصورة</label>
            <input type="text" id="image" name="image" placeholder="masmak.jpg" dir="ltr">
        </div>

        <div class="form-group">
            <label for="map_url">رابط الخريطة (اختياري)</label>
            <input type="url" id="map_url" name="map_url" placeholder="https://maps.google.com/..." dir="ltr">
            <span class="error-msg"></span>
        </div>

        <button type="submit" class="btn">حفظ المكان</button>

    </form>
</section>

<?php include '../includes/admin-footer.php'; ?>
