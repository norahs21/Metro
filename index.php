<?php
/* الصفحة الرئيسية: كل البيانات تنقرأ من قاعدة البيانات (الطالبة 2) */
require_once 'config.php';

$pageTitle = 'الرئيسية';

// 1. أرقام سريعة للواجهة
$stats = $conn->query(
    'SELECT (SELECT COUNT(*) FROM stations)    AS stations,
            (SELECT COUNT(*) FROM places)      AS places,
            (SELECT COUNT(*) FROM metro_lines) AS line_count'
)->fetch_assoc();

// 2. المسارات + محطات كل مسار
$lines        = get_lines($conn);
$stationLines = get_station_lines($conn);
$lineStations = [];
$rows = $conn->query(
    'SELECT sl.line_id, s.id, s.name
     FROM station_lines sl
     JOIN stations s ON s.id = sl.station_id
     ORDER BY sl.line_id, s.id'
)->fetch_all(MYSQLI_ASSOC);
foreach ($rows as $row) {
    $lineStations[$row['line_id']][] = $row;
}

// 3. المحطات المميزة: أكثر 3 محطات فيها أماكن
$featured = $conn->query(
    'SELECT s.id, s.name, s.description, s.image, COUNT(p.id) AS places_count
     FROM stations s
     LEFT JOIN places p ON p.station_id = s.id
     GROUP BY s.id, s.name, s.description, s.image
     ORDER BY places_count DESC, s.id
     LIMIT 3'
)->fetch_all(MYSQLI_ASSOC);

// 4. أحدث الأماكن المضافة
$latest = $conn->query(
    'SELECT p.id, p.name, p.category, p.walk_minutes, p.image, s.id AS station_id, s.name AS station_name
     FROM places p
     JOIN stations s ON s.id = p.station_id
     ORDER BY p.created_at DESC, p.id DESC
     LIMIT 4'
)->fetch_all(MYSQLI_ASSOC);

// 5. كل المحطات للقائمة المنسدلة في الواجهة
$allStations = get_stations($conn);

include 'includes/header.php';
?>

<!-- 1. الواجهة -->
<section class="hero">
    <div class="hero-content">
        <span class="eyebrow"><?php echo icon('sparkle'); ?> دليل مترو الرياض</span>
        <h1>اكتشف وش <span class="highlight">حول محطتك</span></h1>
        <p>دليلك للمقاهي والمطاعم والمعالم اللي تقدر توصلها مشيًا من محطات مترو الرياض. انزل، امشِ دقائق، واستمتع.</p>

        <form class="hero-search" action="station.php" method="get" id="hero-form" novalidate>
            <label for="hero-station" class="sr-only">اختر المحطة</label>
            <span class="hero-search-icon"><?php echo icon('pin'); ?></span>
            <select id="hero-station" name="id" required data-label="المحطة">
                <option value="">وين محطتك؟</option>
                <?php foreach ($allStations as $station): ?>
                    <option value="<?php echo (int) $station['id']; ?>"><?php echo e($station['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">اكتشف <?php echo icon('arrow'); ?></button>
            <span class="error-msg"></span>
        </form>

        <dl class="hero-stats">
            <div>
                <dt>محطة</dt>
                <dd data-count="<?php echo (int) $stats['stations']; ?>"><?php echo (int) $stats['stations']; ?></dd>
            </div>
            <div>
                <dt>مكان قريب</dt>
                <dd data-count="<?php echo (int) $stats['places']; ?>"><?php echo (int) $stats['places']; ?></dd>
            </div>
            <div>
                <dt>مسارات</dt>
                <dd data-count="<?php echo (int) $stats['line_count']; ?>"><?php echo (int) $stats['line_count']; ?></dd>
            </div>
        </dl>
    </div>

    <!-- رسمة خريطة المترو (للزينة فقط) -->
    <div class="hero-art" aria-hidden="true">
        <svg viewBox="0 0 400 320" class="metro-map">
            <path class="track" d="M-20 100 H140 L200 160 L260 220 H420" stroke="#0072CE"/>
            <path class="track" d="M200 -20 V340" stroke="#E30613"/>
            <path class="track" d="M-20 300 H60 L200 160 L280 80 H420" stroke="#FFC72C"/>

            <circle class="train-dot" r="6">
                <animateMotion dur="7s" repeatCount="indefinite" path="M-20 100 H140 L200 160 L260 220 H420"/>
            </circle>
            <circle class="train-dot" r="6">
                <animateMotion dur="5s" repeatCount="indefinite" begin="1s" path="M200 -20 V340"/>
            </circle>
            <circle class="train-dot" r="6">
                <animateMotion dur="8s" repeatCount="indefinite" begin="2s" path="M-20 300 H60 L200 160 L280 80 H420"/>
            </circle>

            <circle class="stop" cx="60" cy="100" r="8" stroke="#0072CE"/>
            <circle class="stop" cx="330" cy="220" r="8" stroke="#0072CE"/>
            <circle class="stop" cx="200" cy="50" r="8" stroke="#E30613"/>
            <circle class="stop" cx="200" cy="270" r="8" stroke="#E30613"/>
            <circle class="stop" cx="120" cy="240" r="8" stroke="#FFC72C"/>
            <circle class="stop" cx="340" cy="80" r="8" stroke="#FFC72C"/>
            <circle class="interchange" cx="200" cy="160" r="16"/>

            <g class="map-pin" transform="translate(300 118)">
                <path d="M0 28s-16-13-16-26a16 16 0 0 1 32 0C16 15 0 28 0 28z"/>
                <circle cx="0" cy="2" r="6"/>
            </g>
        </svg>
    </div>
</section>

<!-- 2. مسارات المترو -->
<section class="lines">
    <div class="section-head">
        <div>
            <span class="kicker">المسارات</span>
            <h2>خطوط المترو ومحطاتها</h2>
        </div>
        <a href="stations.php" class="btn-link">كل المحطات</a>
    </div>

    <div class="lines-grid">
        <?php foreach ($lines as $line): ?>
            <?php $stationsOnLine = $lineStations[$line['id']] ?? []; ?>
            <article class="line-card" style="<?php echo line_style($line['color']); ?>">
                <header class="line-card-head">
                    <span class="line-chip"><?php echo icon('train'); ?></span>
                    <div>
                        <h3><?php echo e($line['name']); ?></h3>
                        <p class="muted"><?php echo stations_label(count($stationsOnLine)); ?> في الدليل</p>
                    </div>
                </header>

                <ol class="route">
                    <?php foreach ($stationsOnLine as $station): ?>
                        <?php $isInterchange = count($stationLines[$station['id']] ?? []) > 1; ?>
                        <li class="<?php echo $isInterchange ? 'interchange' : ''; ?>">
                            <span class="stop"></span>
                            <a href="station.php?id=<?php echo (int) $station['id']; ?>"><?php echo e($station['name']); ?></a>
                            <?php if ($isInterchange): ?>
                                <span class="transfer-tag">محطة تبديل</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <a href="stations.php?line=<?php echo (int) $line['id']; ?>" class="btn-link">محطات <?php echo e($line['name']); ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- 3. محطات مميزة -->
<section class="featured">
    <div class="section-head">
        <div>
            <span class="kicker">الأكثر أماكن</span>
            <h2>محطات مميزة</h2>
        </div>
    </div>

    <div class="cards">
        <?php foreach ($featured as $station): ?>
            <article class="card station-card">
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
    </div>
</section>

<!-- 4. أحدث الأماكن -->
<?php if ($latest): ?>
<section class="latest">
    <div class="section-head">
        <div>
            <span class="kicker">جديد الدليل</span>
            <h2>أحدث الأماكن المضافة</h2>
        </div>
    </div>

    <div class="mini-cards">
        <?php foreach ($latest as $place): ?>
            <a class="mini-card" href="station.php?id=<?php echo (int) $place['station_id']; ?>#place-<?php echo (int) $place['id']; ?>">
                <span class="mini-media"><?php echo place_media($place); ?></span>
                <span class="mini-body">
                    <?php echo category_tag($place['category']); ?>
                    <strong><?php echo e($place['name']); ?></strong>
                    <small><?php echo icon('walk'); ?><?php echo walk_label($place['walk_minutes']); ?> من محطة <?php echo e($place['station_name']); ?></small>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- 5. كيف تستخدم الدليل -->
<section class="steps">
    <div class="section-head">
        <div>
            <span class="kicker">بثلاث خطوات</span>
            <h2>كيف تستخدم الدليل؟</h2>
        </div>
    </div>
    <ol class="steps-list">
        <li>
            <span class="step-icon"><?php echo icon('train'); ?></span>
            <h3>اختر محطتك</h3>
            <p>من الصفحة الرئيسية أو من صفحة المحطات، وفلتر حسب المسار.</p>
        </li>
        <li>
            <span class="step-icon"><?php echo icon('search'); ?></span>
            <h3>اكتشف الأماكن</h3>
            <p>مقاهي ومطاعم ومعالم وحدائق، مرتبة من الأقرب للأبعد.</p>
        </li>
        <li>
            <span class="step-icon"><?php echo icon('walk'); ?></span>
            <h3>امشِ واستمتع</h3>
            <p>كل مكان معه وقت المشي من المحطة ورابط على الخريطة.</p>
        </li>
    </ol>
</section>

<!-- 6. اقترح مكان -->
<section class="cta">
    <div>
        <h2>تعرف مكان حلو حول محطة؟</h2>
        <p>شاركنا اقتراحك، ونضيفه للدليل بعد المراجعة.</p>
    </div>
    <a href="suggest.php" class="btn btn-light"><?php echo icon('send'); ?> اقترح مكان</a>
</section>

<?php include 'includes/footer.php'; ?>
