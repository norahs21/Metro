<?php
/* تعديل مكان: عملية Update (الطالبة 3)
   edit-place.php?id=5 يعرض بيانات المكان في نموذج، وعند الحفظ نحدّث السجل */
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'تعديل مكان';
$stations  = get_stations($conn);
$errors    = [];

// 1. رقم المكان (من الرابط أول مرة، ومن النموذج عند الحفظ)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $conn->prepare(
    'SELECT p.id, p.station_id, p.name, p.category, p.description, p.walk_minutes, p.image, p.map_url,
            p.created_at, s.name AS station_name
     FROM places p
     JOIN stations s ON s.id = p.station_id
     WHERE p.id = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 2. المكان غير موجود
if (!$current) {
    set_flash('error', 'المكان المطلوب غير موجود، يمكن انحذف.');
    redirect('dashboard.php');
}

$place = $current;
$place['description'] = (string) $place['description'];
$place['map_url']     = (string) $place['map_url'];

// 3. حفظ التعديلات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $errors['general'] = 'انتهت صلاحية النموذج، حدّث الصفحة وحاول مرة ثانية.';
    } else {
        [$place, $errors] = validate_place($conn, $_POST);

        $imageError = validate_image($_FILES['image'] ?? null);
        if ($imageError) {
            $errors['image'] = $imageError;
        }
    }

    if (!$errors) {
        $newImage = save_image($_FILES['image'] ?? null);

        if ($newImage === false) {
            $errors['image'] = 'تعذّر حفظ الصورة على الخادم.';
        } else {
            // الصورة النهائية: الجديدة، أو بدون صورة إذا اختار الحذف، أو تبقى القديمة
            $image = $current['image'];
            if ($newImage) {
                $image = $newImage;
            } elseif (!empty($_POST['remove_image'])) {
                $image = null;
            }

            try {
                $stationId = (int) $place['station_id'];
                $minutes   = (int) $place['walk_minutes'];
                $mapUrl    = $place['map_url'] !== '' ? $place['map_url'] : null;

                $stmt = $conn->prepare(
                    'UPDATE places
                     SET station_id = ?, name = ?, category = ?, description = ?, walk_minutes = ?, image = ?, map_url = ?
                     WHERE id = ?'
                );
                $stmt->bind_param('isssissi', $stationId, $place['name'], $place['category'],
                    $place['description'], $minutes, $image, $mapUrl, $id);
                $stmt->execute();
                $stmt->close();

                // نحذف الصورة القديمة من السيرفر إذا تغيّرت
                if ($image !== $current['image']) {
                    delete_place_image($current['image']);
                }

                set_flash('success', 'تم حفظ تعديلات «' . $place['name'] . '» بنجاح.');
                redirect('dashboard.php');
            } catch (mysqli_sql_exception $e) {
                delete_place_image($newImage);
                $errors['general'] = 'صار خطأ في قاعدة البيانات، ما تم حفظ التعديلات.';
            }
        }
    }
}

include __DIR__ . '/../includes/admin-header.php';
?>

<a href="dashboard.php" class="back-link"><?php echo icon('back'); ?> الرجوع للوحة التحكم</a>

<div class="page-header">
    <span class="kicker">تحديث سجل</span>
    <h1>تعديل: <?php echo e($current['name']); ?></h1>
    <p>عدّل البيانات واحفظ التغييرات.</p>
</div>

<?php if ($errors): ?>
    <?php echo alert_box('error', $errors['general'] ?? 'ما تم الحفظ، راجع الحقول المحددة باللون الأحمر.'); ?>
<?php endif; ?>

<div class="admin-form-layout">
    <section class="form-section">
        <?php
        $formAction   = 'edit-place.php?id=' . (int) $id;
        $submitLabel  = 'حفظ التعديلات';
        $currentImage = $current['image'];
        include __DIR__ . '/../includes/place-form.php';
        ?>
    </section>

    <aside class="side-card">
        <h2>الصورة الحالية</h2>
        <div class="image-preview" id="image-preview">
            <?php echo $current['image'] ? place_media($current, '../images/') : icon('image'); ?>
        </div>

        <dl>
            <div><dt>رقم السجل</dt><dd>#<?php echo (int) $current['id']; ?></dd></div>
            <div><dt>المحطة</dt><dd><?php echo e($current['station_name']); ?></dd></div>
            <div><dt>تاريخ الإضافة</dt><dd><?php echo e(date('Y/m/d', strtotime($current['created_at']))); ?></dd></div>
        </dl>
        <br>
        <div class="form-actions">
            <a href="../station.php?id=<?php echo (int) $current['station_id']; ?>#place-<?php echo (int) $current['id']; ?>"
               class="btn-sm btn-neutral" target="_blank" rel="noopener"><?php echo icon('eye'); ?> عرض في الموقع</a>
            <form action="delete-place.php" method="post"
                  data-confirm="هل تريد حذف «<?php echo e($current['name']); ?>»؟ لا يمكن التراجع عن الحذف.">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int) $current['id']; ?>">
                <button type="submit" class="btn-sm btn-delete"><?php echo icon('trash'); ?> حذف المكان</button>
            </form>
        </div>
    </aside>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
