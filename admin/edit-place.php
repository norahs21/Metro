<?php
$pageTitle = 'تعديل مكان';
include '../includes/admin-header.php';
?>

<a href="dashboard.php" class="back-link">&rarr; الرجوع للوحة التحكم</a>

<section class="form-section">
    <h1>تعديل المكان</h1>
    <p class="muted">عدّل البيانات واحفظ التغييرات.</p>
    <br>

    <form id="place-form" class="form" action="edit-place.php" method="post" novalidate>

        <input type="hidden" name="id" value="1">

        <div class="form-group">
            <label for="station_id">المحطة</label>
            <select id="station_id" name="station_id">
                <option value="">اختر المحطة</option>
                <option value="1">المركز المالي (KAFD)</option>
                <option value="2">محطة STC</option>
                <option value="3">المتحف الوطني</option>
                <option value="4" selected>قصر الحكم</option>
                <option value="5">جامعة الملك سعود</option>
                <option value="6">التخصصي</option>
                <option value="7">جامعة الأميرة نورة</option>
                <option value="8">مطار الملك خالد (صالة 1-2)</option>
            </select>
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="name">اسم المكان</label>
            <input type="text" id="name" name="name" value="قصر المصمك">
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="category">النوع</label>
            <select id="category" name="category">
                <option value="">اختر النوع</option>
                <option value="مقهى">مقهى</option>
                <option value="مطعم">مطعم</option>
                <option value="معلم" selected>معلم</option>
                <option value="حديقة">حديقة</option>
                <option value="مول">مول</option>
            </select>
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="description">الوصف</label>
            <textarea id="description" name="description" rows="3">حصن تاريخي من الطين شهد استرداد الرياض عام 1902.</textarea>
        </div>

        <div class="form-group">
            <label for="walk_minutes">المسافة مشيًا (بالدقائق)</label>
            <input type="number" id="walk_minutes" name="walk_minutes" min="1" max="60" value="8">
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="image">اسم ملف الصورة</label>
            <input type="text" id="image" name="image" placeholder="masmak.jpg" dir="ltr" value="masmak.jpg">
        </div>

        <div class="form-group">
            <label for="map_url">رابط الخريطة (اختياري)</label>
            <input type="url" id="map_url" name="map_url" placeholder="https://maps.google.com/..." dir="ltr">
            <span class="error-msg"></span>
        </div>

        <button type="submit" class="btn">حفظ التعديلات</button>

    </form>
</section>

<?php include '../includes/admin-footer.php'; ?>
