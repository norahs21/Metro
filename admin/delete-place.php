<?php
/* حذف مكان: عملية Delete (الطالبة 3)
   - الحذف بطلب POST فقط (مو رابط عادي) + رمز CSRF
   - رسالة التأكيد confirm() تطلع قبلها من ملف script.js */
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

if (!csrf_check()) {
    set_flash('error', 'انتهت صلاحية الطلب، حدّث الصفحة وحاول مرة ثانية.');
    redirect('dashboard.php');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// نجيب اسم المكان وصورته قبل الحذف (للرسالة ولحذف الصورة)
$stmt = $conn->prepare('SELECT name, image FROM places WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$place = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$place) {
    set_flash('error', 'المكان غير موجود، يمكن انحذف من قبل.');
    redirect('dashboard.php');
}

try {
    $stmt = $conn->prepare('DELETE FROM places WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    delete_place_image($place['image']);
    set_flash('success', 'تم حذف «' . $place['name'] . '» بنجاح.');
} catch (mysqli_sql_exception $e) {
    set_flash('error', 'صار خطأ في قاعدة البيانات، ما تم الحذف.');
}

redirect('dashboard.php');
