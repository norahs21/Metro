<?php
/* صفحة المحطات: قائمة المحطات من قاعدة البيانات + بحث وفلترة بالجافاسكربت (الطالبة 2) */
require_once 'config.php';

$pageTitle = 'المحطات';

// المسار المختار من الرابط (مثال: stations.php?line=2) عشان نفعّل زر الفلترة
$selectedLine = filter_input(INPUT_GET, 'line', FILTER_VALIDATE_INT);

$lines        = get_lines($conn);
$stationLines = get_station_lines($conn);

$stations = $conn->query(
    'SELECT s.id, s.name, s.description, s.image, COUNT(p.id) AS places_count
     FROM stations s
     LEFT JOIN places p ON p.station_id = s.id
     GROUP BY s.id, s.name, s.description, s.image
     ORDER BY s.id'
)->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<section class="page-header">
    <span class="kicker">مترو الرياض</span>
    <h1>محطات المترو</h1>
    <p>اختر محطة عشان تشوف الأماكن اللي حولها، أو ابحث باسمها.</p>
</section>

<!-- البحث والفلترة -->
<div class="toolbar">
    <div class="search-box">
        <?php echo icon('search'); ?>
        <label for="station-search" class="sr-only">ابحث باسم المحطة</label>
        <input type="search" id="station-search" placeholder="ابحث باسم المحطة..." autocomplete="off">
    </div>

    <div class="filter-bar" role="group" aria-label="فلترة حسب المسار">
        <button type="button" class="filter-btn <?php echo $selectedLine ? '' : 'active'; ?>" data-line="all">الكل</button>
        <?php foreach ($lines as $line): ?>
            <button type="button"
                    class="filter-btn <?php echo $selectedLine === (int) $line['id'] ? 'active' : ''; ?>"
                    data-line="<?php echo (int) $line['id']; ?>"
                    style="<?php echo line_style($line['color']); ?>">
                <span class="dot"></span><?php echo e($line['name']); ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<p class="results-count" id="results-count" aria-live="polite"><?php echo stations_label(count($stations)); ?></p>

<!-- كروت المحطات -->
<section class="cards" id="stations-grid">
    <?php foreach ($stations as $station): ?>
        <?php
        $lineIds = array_column($stationLines[$station['id']] ?? [], 'id');
        ?>
        <article class="card station-card"
                 data-lines="<?php echo e(implode(' ', $lineIds)); ?>"
                 data-name="<?php echo e($station['name']); ?>">
            <a href="station.php?id=<?php echo (int) $station['id']; ?>" class="card-media">
                <img src="images/<?php echo e($station['image']); ?>" alt="محطة <?php echo e($station['name']); ?>" loading="lazy">
                <span class="count-chip"><?php echo icon('pin'); ?><?php echo places_label($station['places_count']); ?></span>
                <span class="media-badges"><?php echo line_badges($stationLines[$station['id']] ?? []); ?></span>
            </a>
            <div class="card-body">
                <h3><?php echo e($station['name']); ?></h3>
                <p><?php echo e($station['description']); ?></p>
                <a href="station.php?id=<?php echo (int) $station['id']; ?>" class="btn-link">عرض الأماكن</a>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<!-- تظهر إذا ما فيه نتائج -->
<div class="empty-state" id="no-results" hidden>
    <?php echo icon('search', 'icon empty-icon'); ?>
    <h2>ما لقينا محطة بهذا الاسم</h2>
    <p>جرّب كلمة ثانية أو اختر مسار مختلف.</p>
</div>

<?php include 'includes/footer.php'; ?>
