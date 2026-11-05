<?php
/* =========================================================
   admin-functions.php: دوال لوحة التحكم (الطالبة 3)
   1. الجلسة (Session)
   2. رسائل النجاح والخطأ (Flash messages)
   3. الحماية من CSRF
   4. التحقق من بيانات المكان
   5. رفع الصور وحذفها
   ========================================================= */

/* ---------- 1. الجلسة ---------- */

// مدة الخمول المسموحة قبل تسجيل الخروج التلقائي (30 دقيقة)
const SESSION_TIMEOUT = 30 * 60;

function start_admin_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,   // الجافاسكربت ما يقدر يقرأ الكوكي
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/* ---------- 2. رسائل النجاح والخطأ ----------
   نخزن الرسالة في الجلسة، ونعرضها مرة وحدة في الصفحة اللي بعدها */
function set_flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash()
{
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return alert_box($flash['type'], $flash['message']);
}

function alert_box($type, $message)
{
    $icons = ['success' => 'check', 'error' => 'alert', 'info' => 'info'];
    return '<div class="alert alert-' . e($type) . '" role="' . ($type === 'error' ? 'alert' : 'status') . '">'
        . icon($icons[$type] ?? 'info')
        . '<span>' . e($message) . '</span>'
        . '<button type="button" class="alert-close" aria-label="إغلاق">' . icon('x') . '</button>'
        . '</div>';
}

/* ---------- 3. الحماية من CSRF ----------
   كل نموذج في لوحة التحكم يرسل رمز سري مخزن في الجلسة،
   فما يقدر موقع ثاني يرسل طلب حذف أو تعديل باسم المشرف */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_check()
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

/* ---------- 4. التحقق من بيانات المكان ----------
   ترجع [البيانات بعد التنظيف, الأخطاء] */
function validate_place($conn, $input)
{
    $data = [
        'station_id'   => trim($input['station_id'] ?? ''),
        'name'         => trim($input['name'] ?? ''),
        'category'     => trim($input['category'] ?? ''),
        'description'  => trim($input['description'] ?? ''),
        'walk_minutes' => trim($input['walk_minutes'] ?? ''),
        'map_url'      => trim($input['map_url'] ?? ''),
    ];
    $errors = [];

    // المحطة لازم تكون موجودة
    $stationId = filter_var($data['station_id'], FILTER_VALIDATE_INT);
    if (!$stationId) {
        $errors['station_id'] = 'الرجاء اختيار المحطة.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM stations WHERE id = ?');
        $stmt->bind_param('i', $stationId);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) {
            $errors['station_id'] = 'المحطة المختارة غير موجودة.';
        }
        $stmt->close();
    }

    $len = mb_strlen($data['name']);
    if ($len === 0) {
        $errors['name'] = 'الرجاء إدخال اسم المكان.';
    } elseif ($len < 2 || $len > 100) {
        $errors['name'] = 'اسم المكان لازم يكون بين 2 و 100 حرف.';
    }

    if (!array_key_exists($data['category'], CATEGORIES)) {
        $errors['category'] = 'الرجاء اختيار نوع المكان.';
    }

    if (mb_strlen($data['description']) > 1000) {
        $errors['description'] = 'الوصف طويل، الحد الأقصى 1000 حرف.';
    }

    $minutes = filter_var($data['walk_minutes'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 60]]);
    if ($data['walk_minutes'] === '') {
        $errors['walk_minutes'] = 'الرجاء إدخال المسافة مشيًا بالدقائق.';
    } elseif ($minutes === false) {
        $errors['walk_minutes'] = 'المسافة لازم تكون رقم صحيح بين 1 و 60 دقيقة.';
    }

    if ($data['map_url'] !== '') {
        $validUrl = filter_var($data['map_url'], FILTER_VALIDATE_URL)
            && preg_match('#^https?://#i', $data['map_url'])
            && strlen($data['map_url']) <= 255;
        if (!$validUrl) {
            $errors['map_url'] = 'رابط الخريطة غير صحيح، لازم يبدأ بـ https://';
        }
    }

    return [$data, $errors];
}

/* ---------- 5. رفع الصور وحذفها ---------- */

const UPLOAD_MAX_MB = 2;
const UPLOAD_TYPES  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

// يرجع رسالة الخطأ، أو نص فاضي إذا الصورة سليمة (أو ما فيه صورة أصلًا)
function validate_image($file)
{
    if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
        || $file['size'] > UPLOAD_MAX_MB * 1024 * 1024) {
        return 'حجم الصورة أكبر من ' . UPLOAD_MAX_MB . ' ميجابايت.';
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'تعذّر رفع الصورة، حاولي مرة ثانية.';
    }
    // نتأكد من نوع الملف من محتواه نفسه، مو من اسمه
    $info = @getimagesize($file['tmp_name']);
    if (!$info || !array_key_exists($info['mime'], UPLOAD_TYPES)) {
        return 'الصيغ المسموحة للصورة: JPG أو PNG أو WEBP.';
    }
    return '';
}

// يحفظ الصورة باسم عشوائي في images/uploads ويرجع المسار اللي ينحفظ في قاعدة البيانات
function save_image($file)
{
    if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $info = getimagesize($file['tmp_name']);
    $extension = UPLOAD_TYPES[$info['mime']];
    $fileName = 'place_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $folder = __DIR__ . '/../images/uploads/';

    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $folder . $fileName)) {
        return false;
    }
    return 'uploads/' . $fileName;
}

// نحذف الصورة من السيرفر فقط إذا كانت مرفوعة من لوحة التحكم
function delete_place_image($image)
{
    if ($image && strpos($image, 'uploads/') === 0) {
        $path = __DIR__ . '/../images/uploads/' . basename($image);
        if (is_file($path)) {
            unlink($path);
        }
    }
}

// تسميات حالة الاقتراح
function status_label($status)
{
    $labels = ['new' => 'جديد', 'approved' => 'مقبول', 'rejected' => 'مرفوض'];
    return '<span class="status status-' . e($status) . '">' . ($labels[$status] ?? e($status)) . '</span>';
}
