<?php
/* صفحة اقترح مكان: استقبال النموذج ومعالجته بالـ PHP (الطالبة 2)
   1. التحقق من البيانات في الخادم (حتى لو الجافاسكربت مقفل)
   2. الإضافة لجدول suggestions بـ prepared statement
   3. التحويل لنفس الصفحة مع رسالة نجاح (عشان ما يتكرر الإرسال عند تحديث الصفحة) */
require_once 'config.php';

$pageTitle = 'اقترح مكان';
$stations  = get_stations($conn);
$errors    = [];

// القيم القديمة: نرجّعها للنموذج إذا فيه خطأ عشان ما يعيد الزائر الكتابة
$old = [
    'station_id'   => (string) (filter_input(INPUT_GET, 'station', FILTER_VALIDATE_INT) ?: ''),
    'place_name'   => '',
    'category'     => '',
    'note'         => '',
    'visitor_name' => '',
    'email'        => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $value) {
        $old[$key] = trim($_POST[$key] ?? '');
    }

    // --- المحطة: لازم تكون موجودة في قاعدة البيانات ---
    if ($old['station_id'] === '') {
        $errors['station_id'] = 'الرجاء اختيار المحطة.';
    } else {
        $stationId = (int) $old['station_id'];
        $stmt = $conn->prepare('SELECT id FROM stations WHERE id = ?');
        $stmt->bind_param('i', $stationId);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) {
            $errors['station_id'] = 'المحطة المختارة غير موجودة.';
        }
        $stmt->close();
    }

    // --- اسم المكان ---
    $len = mb_strlen($old['place_name']);
    if ($len === 0) {
        $errors['place_name'] = 'الرجاء إدخال اسم المكان.';
    } elseif ($len < 2 || $len > 100) {
        $errors['place_name'] = 'اسم المكان لازم يكون بين 2 و 100 حرف.';
    }

    // --- النوع: لازم يكون من القيم المسموحة ---
    if (!array_key_exists($old['category'], CATEGORIES)) {
        $errors['category'] = 'الرجاء اختيار نوع المكان.';
    }

    // --- الملاحظة (اختيارية) ---
    if (mb_strlen($old['note']) > 500) {
        $errors['note'] = 'الملاحظة طويلة، الحد الأقصى 500 حرف.';
    }

    // --- اسم الزائر: حروف ومسافات فقط ---
    $len = mb_strlen($old['visitor_name']);
    if ($len === 0) {
        $errors['visitor_name'] = 'الرجاء إدخال اسمك.';
    } elseif ($len < 2 || $len > 100) {
        $errors['visitor_name'] = 'الاسم لازم يكون بين 2 و 100 حرف.';
    } elseif (!preg_match('/^[\p{L}\s]+$/u', $old['visitor_name'])) {
        $errors['visitor_name'] = 'الاسم لازم يحتوي على حروف فقط.';
    }

    // --- البريد الإلكتروني ---
    if ($old['email'] === '') {
        $errors['email'] = 'الرجاء إدخال بريدك الإلكتروني.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 100) {
        $errors['email'] = 'صيغة البريد الإلكتروني غير صحيحة، مثال: name@example.com';
    }

    // --- كل شي سليم: نحفظ الاقتراح ---
    if (!$errors) {
        try {
            $stmt = $conn->prepare(
                'INSERT INTO suggestions (station_id, place_name, category, note, visitor_name, email)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param(
                'isssss',
                $stationId,
                $old['place_name'],
                $old['category'],
                $old['note'],
                $old['visitor_name'],
                $old['email']
            );
            $stmt->execute();
            $stmt->close();

            redirect('suggest.php?sent=1');
        } catch (mysqli_sql_exception $e) {
            $errors['general'] = 'صار خطأ أثناء حفظ الاقتراح، حاول مرة ثانية.';
        }
    }
}

$sent = isset($_GET['sent']);

// دالة صغيرة تطبع رسالة الخطأ تحت الحقل
function field_error($errors, $name)
{
    return '<span class="error-msg">' . e($errors[$name] ?? '') . '</span>';
}

// كلاس invalid للحقل اللي فيه خطأ
function invalid($errors, $name)
{
    return isset($errors[$name]) ? ' class="invalid" aria-invalid="true"' : '';
}

include 'includes/header.php';
?>

<section class="page-header">
    <span class="kicker">شاركنا</span>
    <h1>اقترح مكان</h1>
    <p>تعرف مكان حلو قريب من محطة مترو؟ شاركنا، ونضيفه بعد المراجعة.</p>
</section>

<div class="form-layout">

    <?php if ($sent): ?>
        <!-- رسالة النجاح بعد الإرسال -->
        <section class="success-card" role="status">
            <span class="success-icon"><?php echo icon('check'); ?></span>
            <h2>وصلنا اقتراحك، شكرًا لك!</h2>
            <p>بيراجعه فريق الدليل، وإذا تمت الموافقة يظهر في صفحة المحطة.</p>
            <div class="actions-row">
                <a href="suggest.php" class="btn">اقترح مكان آخر</a>
                <a href="stations.php" class="btn btn-ghost">تصفح المحطات</a>
            </div>
        </section>
    <?php else: ?>

    <section class="form-section">
        <?php if ($errors): ?>
            <div class="alert alert-error" role="alert">
                <?php echo icon('alert'); ?>
                <span><?php echo e($errors['general'] ?? 'فيه بيانات ناقصة أو غير صحيحة، راجع الحقول المحددة.'); ?></span>
            </div>
        <?php endif; ?>

        <form id="suggest-form" class="form" action="suggest.php" method="post" novalidate>

            <div class="form-row">
                <div class="form-group">
                    <label for="station_id">المحطة</label>
                    <select id="station_id" name="station_id" required data-label="المحطة"<?php echo invalid($errors, 'station_id'); ?>>
                        <option value="">اختر المحطة</option>
                        <?php foreach ($stations as $station): ?>
                            <option value="<?php echo (int) $station['id']; ?>"
                                <?php if ($old['station_id'] === (string) $station['id']) echo 'selected'; ?>>
                                <?php echo e($station['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo field_error($errors, 'station_id'); ?>
                </div>

                <div class="form-group">
                    <label for="category">نوع المكان</label>
                    <select id="category" name="category" required data-label="نوع المكان"<?php echo invalid($errors, 'category'); ?>>
                        <option value="">اختر النوع</option>
                        <?php foreach (array_keys(CATEGORIES) as $category): ?>
                            <option value="<?php echo e($category); ?>" <?php if ($old['category'] === $category) echo 'selected'; ?>>
                                <?php echo e($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo field_error($errors, 'category'); ?>
                </div>
            </div>

            <div class="form-group">
                <label for="place_name">اسم المكان</label>
                <input type="text" id="place_name" name="place_name" placeholder="مثال: مقهى ..."
                       required minlength="2" maxlength="100" data-label="اسم المكان"
                       value="<?php echo e($old['place_name']); ?>"<?php echo invalid($errors, 'place_name'); ?>>
                <?php echo field_error($errors, 'place_name'); ?>
            </div>

            <div class="form-group">
                <label for="note">ليش تنصح فيه؟ <span class="optional">(اختياري)</span></label>
                <textarea id="note" name="note" rows="4" maxlength="500" data-label="الملاحظة"
                          placeholder="مثال: جلساته هادئة وقهوته مختصة"<?php echo invalid($errors, 'note'); ?>><?php echo e($old['note']); ?></textarea>
                <?php echo field_error($errors, 'note'); ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="visitor_name">اسمك</label>
                    <input type="text" id="visitor_name" name="visitor_name"
                           required minlength="2" maxlength="100" data-label="اسمك" data-pattern="letters"
                           value="<?php echo e($old['visitor_name']); ?>"<?php echo invalid($errors, 'visitor_name'); ?>>
                    <?php echo field_error($errors, 'visitor_name'); ?>
                </div>

                <div class="form-group">
                    <label for="email">بريدك الإلكتروني</label>
                    <input type="email" id="email" name="email" placeholder="example@email.com" dir="ltr"
                           required maxlength="100" data-label="البريد الإلكتروني"
                           value="<?php echo e($old['email']); ?>"<?php echo invalid($errors, 'email'); ?>>
                    <?php echo field_error($errors, 'email'); ?>
                </div>
            </div>

            <button type="submit" class="btn"><?php echo icon('send'); ?> إرسال الاقتراح</button>
        </form>
    </section>
    <?php endif; ?>

    <!-- معلومات جانبية -->
    <aside class="info-card">
        <h2><?php echo icon('info'); ?> وش يصير بعد ما ترسل؟</h2>
        <ol class="walk-timeline compact">
            <li class="origin"><span class="stop"></span><strong>ترسل الاقتراح</strong><small>يوصل للوحة التحكم مباشرة</small></li>
            <li><span class="stop"></span><strong>نراجعه</strong><small>نتأكد من المكان وقربه من المحطة</small></li>
            <li><span class="stop"></span><strong>ينضاف للدليل</strong><small>ويظهر في صفحة المحطة للجميع</small></li>
        </ol>
        <p class="muted">بريدك ما يظهر لأحد، نستخدمه بس إذا احتجنا نتواصل معك.</p>
    </aside>
</div>

<?php include 'includes/footer.php'; ?>
