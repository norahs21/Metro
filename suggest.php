<?php
$pageTitle = 'اقترح مكان';
include 'includes/header.php';
?>

<section class="page-header">
    <h1>اقترح مكان</h1>
    <p>تعرف مكان حلو قريب من محطة مترو؟ شاركنا، ونضيفه بعد المراجعة.</p>
</section>

<section class="form-section">
    <form id="suggest-form" class="form" action="suggest.php" method="post" novalidate>

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
            <label for="place_name">اسم المكان</label>
            <input type="text" id="place_name" name="place_name" placeholder="مثال: مقهى ...">
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="category">نوع المكان</label>
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
            <label for="note">ليش تنصح فيه؟ (اختياري)</label>
            <textarea id="note" name="note" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="visitor_name">اسمك</label>
            <input type="text" id="visitor_name" name="visitor_name">
            <span class="error-msg"></span>
        </div>

        <div class="form-group">
            <label for="email">بريدك الإلكتروني</label>
            <input type="email" id="email" name="email" placeholder="example@email.com" dir="ltr">
            <span class="error-msg"></span>
        </div>

        <button type="submit" class="btn">إرسال الاقتراح</button>

    </form>
</section>

<?php include 'includes/footer.php'; ?>
