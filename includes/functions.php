<?php
/* =========================================================
   functions.php: دوال مساعدة مشتركة (الطالبة 2)
   1. الحماية من XSS
   2. أنواع الأماكن
   3. صياغة الأعداد بالعربي
   4. المسارات والمحطات
   5. الصور والروابط
   ========================================================= */

require_once __DIR__ . '/icons.php';

/* ---------- 1. الحماية من XSS ----------
   أي نص جاي من قاعدة البيانات أو من المستخدم نطبعه عن طريق e()
   عشان تتحول الرموز مثل < > " إلى نص عادي وما تنفذ كـ HTML */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* ---------- 2. أنواع الأماكن ----------
   نفس قيم ENUM في جدول places و suggestions
   القيمة = [الاسم المستخدم في CSS، الأيقونة] */
const CATEGORIES = [
    'مقهى'  => ['cafe', 'coffee'],
    'مطعم'  => ['restaurant', 'utensils'],
    'معلم'  => ['landmark', 'landmark'],
    'حديقة' => ['park', 'tree'],
    'مول'   => ['mall', 'bag'],
];

function category_slug($category)
{
    return CATEGORIES[$category][0] ?? 'landmark';
}

function category_icon($category, $class = 'icon')
{
    return icon(CATEGORIES[$category][1] ?? 'pin', $class);
}

function category_tag($category)
{
    return '<span class="category-tag cat-' . category_slug($category) . '">'
        . category_icon($category) . e($category) . '</span>';
}

/* ---------- 3. صياغة الأعداد بالعربي ----------
   مثال: ar_count(1, 'دقيقة واحدة', 'دقيقتان', 'دقائق', 'دقيقة')  =>  دقيقة واحدة
         ar_count(8, ...)  =>  8 دقائق      ar_count(15, ...)  =>  15 دقيقة */
function ar_count($n, $one, $two, $few, $many, $zero = null)
{
    $n = (int) $n;
    if ($n === 0 && $zero !== null) return $zero;
    if ($n === 1) return $one;
    if ($n === 2) return $two;
    if ($n >= 3 && $n <= 10) return $n . ' ' . $few;
    return $n . ' ' . $many;
}

function walk_label($minutes)
{
    return ar_count($minutes, 'دقيقة واحدة', 'دقيقتان', 'دقائق', 'دقيقة');
}

function places_label($n)
{
    return ar_count($n, 'مكان واحد', 'مكانان', 'أماكن', 'مكانًا', 'لا توجد أماكن بعد');
}

function stations_label($n)
{
    return ar_count($n, 'محطة واحدة', 'محطتان', 'محطات', 'محطة', 'لا توجد محطات');
}

/* ---------- 4. المسارات والمحطات ---------- */

// كل المحطات (للقوائم المنسدلة)
function get_stations($conn)
{
    return $conn->query('SELECT id, name FROM stations ORDER BY id')->fetch_all(MYSQLI_ASSOC);
}

// كل المسارات
function get_lines($conn)
{
    return $conn->query('SELECT id, name, color FROM metro_lines ORDER BY id')->fetch_all(MYSQLI_ASSOC);
}

// مسارات كل محطة: [رقم المحطة => [مسار، مسار...]]
function get_station_lines($conn)
{
    $sql = 'SELECT sl.station_id, l.id, l.name, l.color
            FROM station_lines sl
            JOIN metro_lines l ON l.id = sl.line_id
            ORDER BY l.id';
    $map = [];
    foreach ($conn->query($sql)->fetch_all(MYSQLI_ASSOC) as $row) {
        $map[$row['station_id']][] = $row;
    }
    return $map;
}

// نتأكد إن اللون بصيغة #RRGGBB قبل ما نحطه في CSS
function safe_color($hex)
{
    return preg_match('/^#[0-9A-Fa-f]{6}$/', $hex) ? $hex : '#6B7280';
}

// لون النص المناسب فوق لون المسار (أبيض فوق الأزرق، داكن فوق الأصفر)
function text_on($hex)
{
    $hex = ltrim(safe_color($hex), '#');
    [$r, $g, $b] = array_map('hexdec', str_split($hex, 2));
    $brightness = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return $brightness > 0.6 ? '#1F2933' : '#FFFFFF';
}

// متغيرات CSS للون المسار: style="--line: ...; --line-text: ..."
function line_style($color)
{
    return '--line: ' . safe_color($color) . '; --line-text: ' . text_on($color) . ';';
}

// "المسار الأزرق" => "الأزرق"
function line_short($name)
{
    return trim(str_replace('المسار', '', $name));
}

// شارات المسارات لمحطة معيّنة
function line_badges($lines, $short = true)
{
    $html = '';
    foreach ($lines as $line) {
        $html .= '<span class="line-badge" style="' . line_style($line['color']) . '">'
            . e($short ? line_short($line['name']) : $line['name']) . '</span>';
    }
    return $html;
}

/* ---------- 5. الصور والروابط ---------- */

// صورة المكان، وإذا ما فيه صورة نعرض خلفية بأيقونة النوع
// $base: مسار مجلد الصور نسبةً للصفحة (images/ أو ../images/)
function place_media($place, $base = 'images/')
{
    if (!empty($place['image'])) {
        return '<img src="' . e($base . $place['image']) . '" alt="' . e($place['name']) . '" loading="lazy">';
    }
    return '<div class="media-placeholder cat-' . category_slug($place['category']) . '">'
        . category_icon($place['category']) . '</div>';
}

// رابط الخريطة: إذا ما فيه رابط محفوظ نبحث عن اسم المكان في خرائط قوقل
function map_link($place)
{
    if (!empty($place['map_url'])) {
        return $place['map_url'];
    }
    return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($place['name'] . ' الرياض');
}

// تحويل للصفحة المطلوبة وإيقاف التنفيذ
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}
