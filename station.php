<?php
/* صفحة تفاصيل المحطة: station.php?id=4 (الطالبة 2)
   نقرأ المحطة ومساراتها وأماكنها بـ prepared statements */
require_once 'config.php';

// 1. رقم المحطة من الرابط، لازم يكون رقم صحيح
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$station = null;
if ($id) {
    $stmt = $conn->prepare('SELECT id, name, description, image FROM stations WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $station = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// 2. إذا المحطة غير موجودة نعرض رسالة واضحة
if (!$station) {
    http_response_code(404);
    $pageTitle = 'المحطة غير موجودة';
    include 'includes/header.php';
    ?>
    <div class="empty-state">
        <?php echo icon('train', 'icon empty-icon'); ?>
        <h1>المحطة غير موجودة</h1>
        <p>يمكن الرابط غلط أو المحطة انحذفت من الدليل.</p>
        <a href="stations.php" class="btn">تصفح كل المحطات</a>
    </div>
    <?php
    include 'includes/footer.php';
    exit;
}

// 3. مسارات المحطة
$stmt = $conn->prepare(
    'SELECT l.id, l.name, l.color
     FROM metro_lines l
     JOIN station_lines sl ON sl.line_id = l.id
     WHERE sl.station_id = ?
     ORDER BY l.id'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$lines = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 4. الأماكن القريبة مرتبة من الأقرب
$stmt = $conn->prepare(
    'SELECT id, name, category, description, walk_minutes, image, map_url
     FROM places
     WHERE station_id = ?
     ORDER BY walk_minutes, name'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$places = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 5. عدد الأماكن في كل نوع (لأزرار الفلترة)
$categoryCounts = array_fill_keys(array_keys(CATEGORIES), 0);
foreach ($places as $place) {
    $categoryCounts[$place['category']]++;
}

$pageTitle       = $station['name'];
$pageDescription = 'الأماكن القريبة من محطة ' . $station['name'] . ' في مترو الرياض.';
include 'includes/header.php';
?>

<a href="stations.php" class="back-link"><?php echo icon('back'); ?> الرجوع للمحطات</a>

<!-- معلومات المحطة -->
<section class="station-hero">
    <img src="images/<?php echo e($station['image']); ?>" alt="محطة <?php echo e($station['name']); ?>">
    <div class="station-hero-content">
        <div class="badges"><?php echo line_badges($lines, false); ?></div>
        <h1><?php echo e($station['name']); ?></h1>
        <p><?php echo e($station['description']); ?></p>

        <ul class="station-facts">
            <li><?php echo icon('pin'); ?><?php echo places_label(count($places)); ?></li>
            <?php if ($places): ?>
                <li><?php echo icon('walk'); ?>أقرب مكان: <?php echo walk_label($places[0]['walk_minutes']); ?></li>
            <?php endif; ?>
            <?php if (count($lines) > 1): ?>
                <li><?php echo icon('route'); ?>محطة تبديل بين <?php echo count($lines) === 2 ? 'مسارين' : count($lines) . ' مسارات'; ?></li>
            <?php endif; ?>
        </ul>
    </div>
</section>

<?php if (!$places): ?>
    <!-- محطة بدون أماكن -->
    <div class="empty-state">
        <?php echo icon('pin', 'icon empty-icon'); ?>
        <h2>ما أضفنا أماكن حول هذه المحطة للحين</h2>
        <p>تعرف مكان قريب منها؟ ساعدنا وأضفه للدليل.</p>
        <a href="suggest.php?station=<?php echo (int) $station['id']; ?>" class="btn"><?php echo icon('send'); ?> اقترح مكان</a>
    </div>
<?php else: ?>

<div class="station-layout">
    <section class="places-section">
        <div class="section-head">
            <div>
                <span class="kicker">على بُعد خطوات</span>
                <h2>الأماكن القريبة</h2>
            </div>
            <div class="sort-box">
                <label for="sort-places"><?php echo icon('sort'); ?><span class="sr-only">ترتيب الأماكن</span></label>
                <select id="sort-places">
                    <option value="near">الأقرب أولًا</option>
                    <option value="far">الأبعد أولًا</option>
                    <option value="name">حسب الاسم</option>
                </select>
            </div>
        </div>

        <!-- أزرار فلترة الأماكن -->
        <div class="filter-bar" role="group" aria-label="فلترة حسب النوع">
            <button type="button" class="filter-btn active" data-category="all">الكل <span class="count"><?php echo count($places); ?></span></button>
            <?php foreach ($categoryCounts as $category => $count): ?>
                <button type="button" class="filter-btn" data-category="<?php echo e($category); ?>">
                    <?php echo category_icon($category); ?><?php echo e($category); ?> <span class="count"><?php echo $count; ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <p class="results-count" id="results-count" aria-live="polite"><?php echo places_label(count($places)); ?></p>

        <!-- كروت الأماكن -->
        <div class="cards places-grid" id="places-grid">
            <?php foreach ($places as $place): ?>
                <article class="card place-card" id="place-<?php echo (int) $place['id']; ?>"
                         data-category="<?php echo e($place['category']); ?>"
                         data-minutes="<?php echo (int) $place['walk_minutes']; ?>"
                         data-name="<?php echo e($place['name']); ?>">
                    <div class="card-media">
                        <?php echo place_media($place); ?>
                        <span class="walk-chip"><?php echo icon('walk'); ?><?php echo walk_label($place['walk_minutes']); ?></span>
                    </div>
                    <div class="card-body">
                        <?php echo category_tag($place['category']); ?>
                        <h3><?php echo e($place['name']); ?></h3>
                        <?php if (!empty($place['description'])): ?>
                            <p><?php echo e($place['description']); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo e(map_link($place)); ?>" class="btn-link map-link" target="_blank" rel="noopener">
                            <?php echo icon('pin'); ?> عرض على الخريطة
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="empty-state" id="no-results" hidden>
            <?php echo icon('search', 'icon empty-icon'); ?>
            <h2>ما فيه أماكن من هذا النوع حاليًا</h2>
            <p>اختر نوع ثاني، أو <a href="suggest.php?station=<?php echo (int) $station['id']; ?>">اقترح مكان</a> نضيفه.</p>
        </div>
    </section>

    <!-- مشوار المشي: الأماكن مرتبة كأنها محطات على خط -->
    <aside class="walk-panel">
        <h2><?php echo icon('walk'); ?> مشوارك من المحطة</h2>
        <ol class="walk-timeline">
            <li class="origin">
                <span class="stop"></span>
                <strong>محطة <?php echo e($station['name']); ?></strong>
                <small>نقطة البداية</small>
            </li>
            <?php foreach ($places as $place): ?>
                <li>
                    <span class="stop"></span>
                    <a href="#place-<?php echo (int) $place['id']; ?>"><?php echo e($place['name']); ?></a>
                    <small><?php echo category_icon($place['category']); ?><?php echo walk_label($place['walk_minutes']); ?> مشيًا</small>
                </li>
            <?php endforeach; ?>
        </ol>
    </aside>
</div>

<?php endif; ?>

<!-- دعوة للاقتراح -->
<section class="cta">
    <div>
        <h2>تعرف مكان قريب من <?php echo e($station['name']); ?>؟</h2>
        <p>اقترحه علينا، وبعد المراجعة يظهر هنا للجميع.</p>
    </div>
    <a href="suggest.php?station=<?php echo (int) $station['id']; ?>" class="btn btn-light"><?php echo icon('send'); ?> اقترح مكان</a>
</section>

<?php include 'includes/footer.php'; ?>
