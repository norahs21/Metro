<?php
$pageTitle = 'لوحة التحكم';
include '../includes/admin-header.php';
?>

<div class="dash-header">
    <div>
        <h1>لوحة التحكم</h1>
        <p class="muted">إدارة الأماكن القريبة من المحطات.</p>
    </div>
    <a href="add-place.php" class="btn">+ إضافة مكان</a>
</div>

<!-- مكان رسالة النجاح (الطالبة 3 تعرضها بعد الإضافة أو التعديل أو الحذف) -->
<div class="alert alert-success">تمت إضافة المكان بنجاح.</div>

<!-- جدول الأماكن -->
<section>
    <h2>الأماكن</h2>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المكان</th>
                    <th>المحطة</th>
                    <th>النوع</th>
                    <th>المسافة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>قصر المصمك</td>
                    <td>قصر الحكم</td>
                    <td><span class="category-tag">معلم</span></td>
                    <td>8 دقائق</td>
                    <td class="actions">
                        <a href="edit-place.php?id=1" class="btn-sm btn-edit">تعديل</a>
                        <a href="delete-place.php?id=1" class="btn-sm btn-delete">حذف</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>جامع الإمام تركي بن عبدالله</td>
                    <td>قصر الحكم</td>
                    <td><span class="category-tag">معلم</span></td>
                    <td>3 دقائق</td>
                    <td class="actions">
                        <a href="edit-place.php?id=2" class="btn-sm btn-edit">تعديل</a>
                        <a href="delete-place.php?id=2" class="btn-sm btn-delete">حذف</a>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>المتحف الوطني السعودي</td>
                    <td>المتحف الوطني</td>
                    <td><span class="category-tag">معلم</span></td>
                    <td>5 دقائق</td>
                    <td class="actions">
                        <a href="edit-place.php?id=4" class="btn-sm btn-edit">تعديل</a>
                        <a href="delete-place.php?id=4" class="btn-sm btn-delete">حذف</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- جدول الاقتراحات -->
<section>
    <h2>اقتراحات الزوار</h2>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>اسم المكان</th>
                    <th>المحطة</th>
                    <th>النوع</th>
                    <th>المقترح</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>مقهى تجريبي</td>
                    <td>التخصصي</td>
                    <td><span class="category-tag">مقهى</span></td>
                    <td>سارة</td>
                    <td><span class="status status-new">جديد</span></td>
                </tr>
                <tr>
                    <td>مطعم تجريبي</td>
                    <td>المركز المالي (KAFD)</td>
                    <td><span class="category-tag">مطعم</span></td>
                    <td>نورة</td>
                    <td><span class="status status-new">جديد</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php include '../includes/admin-footer.php'; ?>
