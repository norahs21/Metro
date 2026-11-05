<?php
/* إضافة مكان جديد: عملية Create (الطالبة 3)
   ممكن نفتحها من اقتراح زائر: add-place.php?suggestion=3
   فتنعبّى الحقول من الاقتراح، وبعد الحفظ تتغير حالة الاقتراح إلى "مقبول" */
require_once __DIR__ . '/../includes/auth.php';

$pageTitle    = 'إضافة مكان';
$stations     = get_stations($conn);
$errors       = [];
$suggestionId = 0;

$place = [
    'station_id'   => '',
    'name'         => '',
    'category'     => '',
    'description'  => '',
    'walk_minutes' => '',
    'map_url'      => '',
];

// 1. تعبئة الحقول من اقتراح زائر
$fromSuggestion = filter_input(INPUT_GET, 'suggestion', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_POST, 'suggestion_id', FILTER_VALIDATE_INT);

$suggestion = null;
if ($fromSuggestion) {
    $stmt = $conn->prepare("SELECT id, station_id, place_name, category, note, visitor_name FROM suggestions WHERE id = ? AND status = 'new'");
    $stmt->bind_param('i', $fromSuggestion);
    $stmt->execute();
    $suggestion = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($suggestion) {
        $suggestionId = (int) $suggestion['id'];
        $place['station_id']  = $suggestion['station_id'];
        $place['name']        = $suggestion['place_name'];
        $place['category']    = $suggestion['category'];
        $place['description'] = (string) $suggestion['note'];
    }
}

// 2. استقبال النموذج
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
        $image = save_image($_FILES['image'] ?? null);

        if ($image === false) {
            $errors['image'] = 'تعذّر حفظ الصورة على الخادم.';
        } else {
            try {
                $stationId  = (int) $place['station_id'];
                $minutes    = (int) $place['walk_minutes'];
                $mapUrl     = $place['map_url'] !== '' ? $place['map_url'] : null;

                $stmt = $conn->prepare(
                    'INSERT INTO places (station_id, name, category, description, walk_minutes, image, map_url)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->bind_param('isssiss', $stationId, $place['name'], $place['category'],
                    $place['description'], $minutes, $image, $mapUrl);
                $stmt->execute();
                $stmt->close();

                // إذا الإضافة من اقتراح، نخليه "مقبول"
                if ($suggestionId) {
                    $stmt = $conn->prepare("UPDATE suggestions SET status = 'approved' WHERE id = ?");
                    $stmt->bind_param('i', $suggestionId);
                    $stmt->execute();
                    $stmt->close();
                }

                set_flash('success', 'تمت إضافة «' . $place['name'] . '» بنجاح'
                    . ($suggestionId ? '، وتم قبول اقتراح الزائر.' : '.'));
                redirect('dashboard.php');
            } catch (mysqli_sql_exception $e) {
                delete_place_image($image); // الصورة ما لها داعي إذا ما انحفظ المكان
                $errors['general'] = 'صار خطأ في قاعدة البيانات، ما تمت الإضافة.';
            }
        }
    }
}

include __DIR__ . '/../includes/admin-header.php';
?>

<a href="dashboard.php" class="back-link"><?php echo icon('back'); ?> الرجوع للوحة التحكم</a>

<div class="page-header">
    <span class="kicker">إضافة سجل جديد</span>
    <h1>إضافة مكان جديد</h1>
    <p>عبّي بيانات المكان القريب من المحطة، وبعد الحفظ يظهر مباشرة في الموقع.</p>
</div>

<?php if ($errors): ?>
    <?php echo alert_box('error', $errors['general'] ?? 'ما تمت الإضافة، راجع الحقول المحددة باللون الأحمر.'); ?>
<?php endif; ?>

<?php if ($suggestion): ?>
    <div class="alert alert-info from-suggestion">
        <?php echo icon('inbox'); ?>
        <span>البيانات معبّاة من اقتراح الزائر <strong><?php echo e($suggestion['visitor_name']); ?></strong>. كمّل المسافة مشيًا والصورة ثم احفظ.</span>
    </div>
<?php endif; ?>

<div class="admin-form-layout">
    <section class="form-section">
        <?php
        $formAction  = 'add-place.php';
        $submitLabel = 'حفظ المكان';
        include __DIR__ . '/../includes/place-form.php';
        ?>
    </section>

    <aside class="side-card">
        <h2>معاينة الصورة</h2>
        <div class="image-preview" id="image-preview">
            <?php echo icon('image'); ?>
        </div>
        <p class="hint">بعد ما تختار صورة تظهر معاينتها هنا قبل الرفع.</p>
    </aside>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
