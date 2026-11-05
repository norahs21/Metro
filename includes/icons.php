<?php
/* =========================================================
   أيقونات SVG بسيطة نستخدمها في كل الموقع
   الاستخدام: echo icon('train');
   ========================================================= */

function icon($name, $class = 'icon')
{
    $paths = [
        'train'    => '<rect x="5" y="3" width="14" height="13" rx="3"/><path d="M5 10h14M8 20l2-4M16 20l-2-4"/><circle cx="9" cy="13" r=".6"/><circle cx="15" cy="13" r=".6"/>',
        'pin'      => '<path d="M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
        'walk'     => '<circle cx="13" cy="4.5" r="1.8"/><path d="M13 8l-1.5 6M11.5 14L9 21M11.5 14l3 3 .5 4M12.6 9.2L9 11l-1 3M13 8l2.5 3 2.5 1"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'search'   => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
        'moon'     => '<path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5z"/>',
        'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
        'plus'     => '<path d="M12 5v14M5 12h14"/>',
        'edit'     => '<path d="M4 20h4L19 9l-4-4L4 16v4z"/><path d="M13.5 6.5l4 4"/>',
        'trash'    => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
        'logout'   => '<path d="M9 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h3M14 17l5-5-5-5M19 12H9"/>',
        'check'    => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'x'        => '<path d="M6 6l12 12M18 6L6 18"/>',
        'coffee'   => '<path d="M4 9h12v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V9z"/><path d="M16 10h1.5a2.5 2.5 0 0 1 0 5H16M8 3v3M12 3v3"/>',
        'utensils' => '<path d="M7 3v8M5 3v5a2 2 0 0 0 4 0V3M7 11v10M17 21V3c-2 1-3 3.5-3 7v3h3"/>',
        'landmark' => '<path d="M3 21h18M4 18h16M12 3l8 5H4l8-5zM6 10v8M10 10v8M14 10v8M18 10v8"/>',
        'tree'     => '<path d="M12 22v-6"/><path d="M12 3a5 5 0 0 1 5 5 4 4 0 0 1-1 7.9H8A4 4 0 0 1 7 8a5 5 0 0 1 5-5z"/>',
        'bag'      => '<path d="M5 8h14l-1 13H6L5 8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
        'arrow'    => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
        'back'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'eye'      => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'eye-off'  => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/>',
        'inbox'    => '<path d="M3 13l3-8h12l3 8v6H3v-6z"/><path d="M3 13h5l1 3h6l1-3h5"/>',
        'grid'     => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'external' => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
        'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'lock'     => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'send'     => '<path d="M21 3L3 10.5l7 3 3 7L21 3z"/><path d="M10 13.5L21 3"/>',
        'image'    => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-9 9"/>',
        'route'    => '<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M8.5 6H15a3 3 0 0 1 0 6H9a3 3 0 0 0 0 6h6.5"/>',
        'info'     => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8v.01"/>',
        'alert'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 16.5v.01"/>',
        'sparkle'  => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z"/><path d="M19 16l.7 1.8 1.8.7-1.8.7L19 21l-.7-1.8-1.8-.7 1.8-.7L19 16z"/>',
        'sort'     => '<path d="M7 4v16M3 8l4-4 4 4M17 20V4M13 16l4 4 4-4"/>',
    ];

    if (!isset($paths[$name])) {
        return '';
    }

    return '<svg class="' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" '
        . 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . $paths[$name] . '</svg>';
}
