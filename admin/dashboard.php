<?php
/* لوحة التحكم (الطالبة 3)
   - Read: عرض كل الأماكن والاقتراحات من قاعدة البيانات
   - روابط Create / Update، ونماذج Delete مع confirm() */
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'لوحة التحكم';

// 1. الأرقام في أعلى الصفحة
$stats = $conn->query(
    "SELECT (SELECT COUNT(*) FROM places)                           AS places,
            (SELECT COUNT(*) FROM stations)                         AS stations,
            (SELECT COUNT(*) FROM suggestions WHERE status = 'new') AS new_suggestions,
            (SELECT COUNT(*) FROM suggestions)                      AS suggestions"
)->fetch_assoc();

// 2. جدول الأماكن مع اسم المحطة
$places = $conn->query(
    'SELECT p.id, p.name, p.category, p.walk_minutes, p.image, p.created_at,
            p.station_id, s.name AS station_name
     FROM places p
     JOIN stations s ON s.id = p.station_id
     ORDER BY p.id DESC'
)->fetch_all(MYSQLI_ASSOC);

// 3. جدول الاقتراحات (الجديدة أولًا)
$suggestions = $conn->query(
    "SELECT g.id, g.place_name, g.category, g.note, g.visitor_name, g.email, g.status, g.created_at,
            s.name AS station_name
     FROM suggestions g
     JOIN stations s ON s.id = g.station_id
     ORDER BY FIELD(g.status, 'new', 'approved', 'rejected'), g.created_at DESC, g.id DESC"
)->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="dash-header">
    <div>
        <span class="kicker">أهلًا <?php echo e($_SESSION['admin_username']); ?></span>
        <h1>لوحة التحكم</h1>
        <p class="muted">إدارة الأماكن القريبة من المحطات واقتراحات الزوار.</p>
    </div>
    <a href="add-place.php" class="btn"><?php echo icon('plus'); ?> إضافة مكان</a>
</div>

<!-- الأرقام -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-icon"><?php echo icon('pin'); ?></span>
        <div><strong><?php echo (int) $stats['places']; ?></strong><span>مكان في الدليل</span></div>
    </div>
    <div class="stat-card blue">
        <span class="stat-icon"><?php echo icon('train'); ?></span>
        <div><strong><?php echo (int) $stats['stations']; ?></strong><span>محطة</span></div>
    </div>
    <div class="stat-card yellow">
        <span class="stat-icon"><?php echo icon('inbox'); ?></span>
        <div><strong><?php echo (int) $stats['new_suggestions']; ?></strong><span>اقتراح جديد بانتظار المراجعة</span></div>
    </div>
    <div class="stat-card red">
        <span class="stat-icon"><?php echo icon('send'); ?></span>
        <div><strong><?php echo (int) $stats['suggestions']; ?></strong><span>إجمالي الاقتراحات</span></div>
    </div>
</div>

<!-- جدول الأماكن -->
<section class="panel" id="places">
    <div class="panel-head">
        <h2><?php echo icon('pin'); ?> الأماكن <span class="pill-count"><?php echo count($places); ?></span></h2>
        <div class="search-box">
            <?php echo icon('search'); ?>
            <label for="places-search" class="sr-only">ابحث في الأماكن</label>
            <input type="search" id="places-search" placeholder="ابحث بالاسم أو المحطة..." data-table-search="places-table" autocomplete="off">
        </div>
    </div>

    <div class="table-wrap">
        <table class="table" id="places-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المكان</th>
                    <th>المحطة</th>
                    <th>النوع</th>
                    <th>المسافة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$places): ?>
                    <tr class="empty-row"><td colspan="6">ما فيه أماكن للحين. <a href="add-place.php">أضف أول مكان</a></td></tr>
                <?php endif; ?>

                <?php foreach ($places as $place): ?>
                    <tr>
                        <td><?php echo (int) $place['id']; ?></td>
                        <td>
                            <div class="place-cell">
                                <span class="thumb"><?php echo place_media($place, '../images/'); ?></span>
                                <div>
                                    <strong><?php echo e($place['name']); ?></strong>
                                    <small>أُضيف <?php echo e(date('Y/m/d', strtotime($place['created_at']))); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo e($place['station_name']); ?></td>
                        <td><?php echo category_tag($place['category']); ?></td>
                        <td><?php echo walk_label($place['walk_minutes']); ?></td>
                        <td>
                            <div class="actions">
                                <a href="../station.php?id=<?php echo (int) $place['station_id']; ?>#place-<?php echo (int) $place['id']; ?>"
                                   class="btn-sm btn-neutral" target="_blank" rel="noopener" title="عرض في الموقع">
                                    <?php echo icon('eye'); ?><span class="sr-only">عرض</span>
                                </a>
                                <a href="edit-place.php?id=<?php echo (int) $place['id']; ?>" class="btn-sm btn-edit">
                                    <?php echo icon('edit'); ?> تعديل
                                </a>
                                <form action="delete-place.php" method="post"
                                      data-confirm="هل تريد حذف «<?php echo e($place['name']); ?>»؟ لا يمكن التراجع عن الحذف.">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $place['id']; ?>">
                                    <button type="submit" class="btn-sm btn-delete"><?php echo icon('trash'); ?> حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="empty-row no-match-row" hidden><td colspan="6">ما فيه نتائج مطابقة للبحث.</td></tr>
            </tbody>
        </table>
    </div>
</section>

<!-- جدول الاقتراحات -->
<section class="panel" id="suggestions">
    <div class="panel-head">
        <h2><?php echo icon('inbox'); ?> اقتراحات الزوار <span class="pill-count"><?php echo count($suggestions); ?></span></h2>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>المكان المقترح</th>
                    <th>المحطة</th>
                    <th>النوع</th>
                    <th>الملاحظة</th>
                    <th>المقترح</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$suggestions): ?>
                    <tr class="empty-row"><td colspan="7">ما وصلت اقتراحات للحين.</td></tr>
                <?php endif; ?>

                <?php foreach ($suggestions as $suggestion): ?>
                    <tr>
                        <td>
                            <strong><?php echo e($suggestion['place_name']); ?></strong>
                            <small><?php echo e(date('Y/m/d', strtotime($suggestion['created_at']))); ?></small>
                        </td>
                        <td><?php echo e($suggestion['station_name']); ?></td>
                        <td><?php echo category_tag($suggestion['category']); ?></td>
                        <td class="wrap"><?php echo $suggestion['note'] !== null && $suggestion['note'] !== '' ? e($suggestion['note']) : '—'; ?></td>
                        <td>
                            <?php echo e($suggestion['visitor_name']); ?>
                            <small dir="ltr"><?php echo e($suggestion['email']); ?></small>
                        </td>
                        <td><?php echo status_label($suggestion['status']); ?></td>
                        <td>
                            <div class="actions">
                                <?php if ($suggestion['status'] === 'new'): ?>
                                    <a href="add-place.php?suggestion=<?php echo (int) $suggestion['id']; ?>" class="btn-sm btn-edit">
                                        <?php echo icon('check'); ?> إضافة كمكان
                                    </a>
                                    <form action="suggestion-action.php" method="post">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $suggestion['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-sm btn-neutral"><?php echo icon('x'); ?> رفض</button>
                                    </form>
                                <?php elseif ($suggestion['status'] === 'rejected'): ?>
                                    <form action="suggestion-action.php" method="post">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $suggestion['id']; ?>">
                                        <input type="hidden" name="action" value="restore">
                                        <button type="submit" class="btn-sm btn-neutral">إرجاع للمراجعة</button>
                                    </form>
                                <?php endif; ?>
                                <form action="suggestion-action.php" method="post"
                                      data-confirm="هل تريد حذف اقتراح «<?php echo e($suggestion['place_name']); ?>»؟">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $suggestion['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn-sm btn-delete"><?php echo icon('trash'); ?> حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
