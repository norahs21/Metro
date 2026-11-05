<?php
/* نموذج المكان المشترك بين صفحة الإضافة وصفحة التعديل (الطالبة 3)
   المتغيرات المطلوبة قبل التضمين:
   $place (قيم الحقول)، $errors، $stations، $formAction، $submitLabel
   واختياري: $suggestionId (إذا الإضافة من اقتراح زائر)، $currentImage (في التعديل) */

$suggestionId = $suggestionId ?? 0;
$currentImage = $currentImage ?? '';

// كلاس invalid + رسالة الخطأ لكل حقل
$invalid = function ($name) use ($errors) {
    return isset($errors[$name]) ? ' class="invalid" aria-invalid="true"' : '';
};
$errorMsg = function ($name) use ($errors) {
    return '<span class="error-msg">' . e($errors[$name] ?? '') . '</span>';
};
?>
<form id="place-form" class="form" action="<?php echo e($formAction); ?>" method="post" enctype="multipart/form-data" novalidate>
    <?php echo csrf_field(); ?>
    <?php if ($suggestionId): ?>
        <input type="hidden" name="suggestion_id" value="<?php echo (int) $suggestionId; ?>">
    <?php endif; ?>

    <div class="form-row">
        <div class="form-group">
            <label for="station_id">المحطة</label>
            <select id="station_id" name="station_id" required data-label="المحطة"<?php echo $invalid('station_id'); ?>>
                <option value="">اختر المحطة</option>
                <?php foreach ($stations as $station): ?>
                    <option value="<?php echo (int) $station['id']; ?>"
                        <?php if ((string) $place['station_id'] === (string) $station['id']) echo 'selected'; ?>>
                        <?php echo e($station['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php echo $errorMsg('station_id'); ?>
        </div>

        <div class="form-group">
            <label for="category">النوع</label>
            <select id="category" name="category" required data-label="النوع"<?php echo $invalid('category'); ?>>
                <option value="">اختر النوع</option>
                <?php foreach (array_keys(CATEGORIES) as $category): ?>
                    <option value="<?php echo e($category); ?>" <?php if ($place['category'] === $category) echo 'selected'; ?>>
                        <?php echo e($category); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php echo $errorMsg('category'); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="name">اسم المكان</label>
        <input type="text" id="name" name="name" required minlength="2" maxlength="100" data-label="اسم المكان"
               value="<?php echo e($place['name']); ?>"<?php echo $invalid('name'); ?>>
        <?php echo $errorMsg('name'); ?>
    </div>

    <div class="form-group">
        <label for="description">الوصف <span class="optional">(اختياري)</span></label>
        <textarea id="description" name="description" rows="4" maxlength="1000" data-label="الوصف"<?php echo $invalid('description'); ?>><?php echo e($place['description']); ?></textarea>
        <?php echo $errorMsg('description'); ?>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="walk_minutes">المسافة مشيًا (بالدقائق)</label>
            <input type="number" id="walk_minutes" name="walk_minutes" min="1" max="60" required
                   data-label="المسافة مشيًا" value="<?php echo e($place['walk_minutes']); ?>"<?php echo $invalid('walk_minutes'); ?>>
            <?php echo $errorMsg('walk_minutes'); ?>
        </div>

        <div class="form-group">
            <label for="map_url">رابط الخريطة <span class="optional">(اختياري)</span></label>
            <input type="url" id="map_url" name="map_url" placeholder="https://maps.google.com/..." dir="ltr" maxlength="255"
                   data-label="رابط الخريطة" value="<?php echo e($place['map_url']); ?>"<?php echo $invalid('map_url'); ?>>
            <?php echo $errorMsg('map_url'); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="image"><?php echo $currentImage ? 'تغيير الصورة' : 'صورة المكان'; ?> <span class="optional">(اختياري)</span></label>
        <!-- زر رفع بنص عربي بدل زر المتصفح الافتراضي (Choose File) -->
        <div class="file-input">
            <input type="file" id="image" name="image" class="file-native" accept="image/jpeg,image/png,image/webp"
                   data-label="الصورة" data-max-size="<?php echo UPLOAD_MAX_MB; ?>" data-preview="image-preview"<?php echo $invalid('image'); ?>>
            <label for="image" class="file-btn"><?php echo icon('image'); ?> اختر صورة</label>
            <span class="file-name" data-empty="لم يتم اختيار صورة">لم يتم اختيار صورة</span>
        </div>
        <span class="hint">JPG أو PNG أو WEBP، بحد أقصى <?php echo UPLOAD_MAX_MB; ?> ميجابايت. إذا ما اخترت صورة تظهر أيقونة النوع.</span>
        <?php echo $errorMsg('image'); ?>
    </div>

    <?php if ($currentImage): ?>
        <label class="checkbox">
            <input type="checkbox" name="remove_image" value="1">
            حذف الصورة الحالية
        </label>
    <?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn"><?php echo icon('check'); ?> <?php echo e($submitLabel); ?></button>
        <a href="dashboard.php" class="btn btn-ghost">إلغاء</a>
    </div>
</form>
