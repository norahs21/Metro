<?php
/* إجراءات اقتراحات الزوار (الطالبة 3)
   reject: رفض الاقتراح | restore: إرجاعه للمراجعة | delete: حذفه نهائيًا
   (القبول يصير من صفحة add-place.php?suggestion=رقم) */
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php#suggestions');
}

if (!csrf_check()) {
    set_flash('error', 'انتهت صلاحية الطلب، حدّث الصفحة وحاول مرة ثانية.');
    redirect('dashboard.php#suggestions');
}

$id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

$stmt = $conn->prepare('SELECT place_name FROM suggestions WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$suggestion = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$suggestion) {
    set_flash('error', 'الاقتراح غير موجود.');
    redirect('dashboard.php#suggestions');
}

$name = $suggestion['place_name'];

try {
    if ($action === 'reject' || $action === 'restore') {
        $status = $action === 'reject' ? 'rejected' : 'new';
        $stmt = $conn->prepare('UPDATE suggestions SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $stmt->close();

        set_flash('success', $action === 'reject'
            ? 'تم رفض اقتراح «' . $name . '».'
            : 'تم إرجاع اقتراح «' . $name . '» للمراجعة.');
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare('DELETE FROM suggestions WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        set_flash('success', 'تم حذف اقتراح «' . $name . '» بنجاح.');
    } else {
        set_flash('error', 'إجراء غير معروف.');
    }
} catch (mysqli_sql_exception $e) {
    set_flash('error', 'صار خطأ في قاعدة البيانات، ما تم تنفيذ الإجراء.');
}

redirect('dashboard.php#suggestions');
