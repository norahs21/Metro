/* =========================================================
   حول المحطة: ملف الجافاسكربت (الطالبة 2 + الطالبة 3)
   1. الوضع الليلي
   2. أدوات مساعدة
   3. البحث والفلترة في صفحة المحطات
   4. فلترة وترتيب الأماكن في صفحة المحطة
   5. التحقق من النماذج برسائل عربية
   6. تأكيد الحذف بـ confirm()
   7. إضافات: عدّاد الأرقام، عدّاد الأحرف، معاينة الصورة،
      إظهار كلمة المرور، إغلاق التنبيهات، البحث في جدول لوحة التحكم
   ========================================================= */

/* ---------- 1. الوضع الليلي ----------
   الملف محمّل في الـ head، فنطبّق الوضع المحفوظ فورًا قبل ما تنرسم الصفحة
   (عشان ما تومض الصفحة باللون الأبيض) */
(function applySavedTheme() {
    try {
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    } catch (e) {
        // بعض المتصفحات تمنع localStorage في الوضع الخاص، نتجاهل الخطأ
    }
})();

function initThemeToggle() {
    const button = document.getElementById('theme-toggle');
    if (!button) return;

    const label = button.querySelector('.theme-label');

    function updateButton() {
        const isDark = document.documentElement.classList.contains('dark');
        button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        if (label) label.textContent = isDark ? 'الوضع النهاري' : 'الوضع الليلي';
    }

    button.addEventListener('click', function () {
        const isDark = document.documentElement.classList.toggle('dark');
        try {
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        } catch (e) { /* تجاهل */ }
        updateButton();
    });

    updateButton();
}

/* ---------- 2. أدوات مساعدة ---------- */

// توحيد الحروف العربية للبحث: (أ إ آ => ا) (ة => ه) (ى => ي) وإزالة التشكيل
function normalizeArabic(text) {
    return text
        .toLowerCase()
        .replace(/[ً-ْ]/g, '')
        .replace(/[أإآ]/g, 'ا')
        .replace(/ة/g, 'ه')
        .replace(/ى/g, 'ي')
        .trim();
}

// صياغة العدد بالعربي: arCount(3, ['محطة واحدة', 'محطتان', 'محطات', 'محطة'])
function arCount(n, forms) {
    if (n === 0) return 'لا توجد نتائج';
    if (n === 1) return forms[0];
    if (n === 2) return forms[1];
    if (n >= 3 && n <= 10) return n + ' ' + forms[2];
    return n + ' ' + forms[3];
}

// تفعيل زر واحد من مجموعة أزرار الفلترة
function setActiveButton(buttons, activeButton) {
    buttons.forEach(function (btn) {
        btn.classList.toggle('active', btn === activeButton);
        btn.setAttribute('aria-pressed', btn === activeButton ? 'true' : 'false');
    });
}

/* ---------- 3. البحث والفلترة في صفحة المحطات ---------- */
function initStationFilters() {
    const grid = document.getElementById('stations-grid');
    if (!grid) return;

    const cards = grid.querySelectorAll('.station-card');
    const buttons = document.querySelectorAll('.filter-btn[data-line]');
    const searchInput = document.getElementById('station-search');
    const counter = document.getElementById('results-count');
    const emptyState = document.getElementById('no-results');

    function applyFilters() {
        const activeButton = document.querySelector('.filter-btn[data-line].active');
        const line = activeButton ? activeButton.dataset.line : 'all';
        const query = normalizeArabic(searchInput ? searchInput.value : '');
        let visible = 0;

        cards.forEach(function (card) {
            const cardLines = card.dataset.lines.split(' ');
            const matchLine = line === 'all' || cardLines.includes(line);
            const matchName = normalizeArabic(card.dataset.name).includes(query);
            const show = matchLine && matchName;

            card.hidden = !show;
            if (show) visible++;
        });

        counter.textContent = arCount(visible, ['محطة واحدة', 'محطتان', 'محطات', 'محطة']);
        emptyState.hidden = visible > 0;
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            setActiveButton(buttons, button);
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    applyFilters(); // تطبيق الفلتر المختار من الرابط (stations.php?line=2)
}

/* ---------- 4. فلترة وترتيب الأماكن في صفحة المحطة ---------- */
function initPlaceFilters() {
    const grid = document.getElementById('places-grid');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('.place-card'));
    const buttons = document.querySelectorAll('.filter-btn[data-category]');
    const sortSelect = document.getElementById('sort-places');
    const counter = document.getElementById('results-count');
    const emptyState = document.getElementById('no-results');

    function applyFilter(category) {
        let visible = 0;
        cards.forEach(function (card) {
            const show = category === 'all' || card.dataset.category === category;
            card.hidden = !show;
            if (show) visible++;
        });
        counter.textContent = arCount(visible, ['مكان واحد', 'مكانان', 'أماكن', 'مكانًا']);
        emptyState.hidden = visible > 0;
    }

    function applySort(type) {
        const sorted = cards.slice().sort(function (a, b) {
            if (type === 'name') {
                return a.dataset.name.localeCompare(b.dataset.name, 'ar');
            }
            const diff = Number(a.dataset.minutes) - Number(b.dataset.minutes);
            return type === 'far' ? -diff : diff;
        });
        // appendChild ينقل العنصر لآخر القائمة، فنعيد ترتيب الكروت في الصفحة
        sorted.forEach(function (card) {
            grid.appendChild(card);
        });
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            setActiveButton(buttons, button);
            applyFilter(button.dataset.category);
        });
    });

    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            applySort(sortSelect.value);
        });
    }
}

/* ---------- 5. التحقق من النماذج برسائل عربية ----------
   القواعد مكتوبة كـ attributes في الـ HTML:
   required / minlength / maxlength / min / max / type="email" / type="url"
   data-pattern="letters" / data-max-size (للصور بالميجابايت)
   ونستخدم data-label عشان نكتب اسم الحقل في رسالة الخطأ */

function getFieldError(field) {
    const value = field.value.trim();
    const label = field.dataset.label || 'هذا الحقل';
    const isSelect = field.tagName === 'SELECT';

    // الحقول الإجبارية
    if (field.required && field.type !== 'file' && value === '') {
        return isSelect ? 'الرجاء اختيار ' + label + '.' : 'الرجاء إدخال ' + label + '.';
    }

    // حقل الصورة
    if (field.type === 'file') {
        if (field.required && field.files.length === 0) {
            return 'الرجاء اختيار ' + label + '.';
        }
        if (field.files.length > 0) {
            const file = field.files[0];
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];
            const maxMb = Number(field.dataset.maxSize || 2);
            if (!allowed.includes(file.type)) {
                return 'الصيغ المسموحة للصورة: JPG أو PNG أو WEBP.';
            }
            if (file.size > maxMb * 1024 * 1024) {
                return 'حجم الصورة أكبر من ' + maxMb + ' ميجابايت.';
            }
        }
        return '';
    }

    // الحقول الاختيارية الفاضية ما نتحقق منها
    if (value === '') return '';

    const minLength = Number(field.getAttribute('minlength'));
    const maxLength = Number(field.getAttribute('maxlength'));
    if (minLength && value.length < minLength) {
        return label + ' قصير جدًا، لازم يكون ' + minLength + ' أحرف على الأقل.';
    }
    if (maxLength && value.length > maxLength) {
        return label + ' طويل جدًا، الحد الأقصى ' + maxLength + ' حرفًا.';
    }

    if (field.type === 'email') {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
        if (!emailPattern.test(value)) {
            return 'صيغة البريد الإلكتروني غير صحيحة، مثال: name@example.com';
        }
    }

    if (field.type === 'url') {
        if (!/^https?:\/\/[^\s]+\.[^\s]+$/i.test(value)) {
            return 'الرابط غير صحيح، لازم يبدأ بـ https://';
        }
    }

    if (field.type === 'number') {
        const number = Number(value);
        const min = Number(field.min);
        const max = Number(field.max);
        if (!Number.isInteger(number) || number < min || number > max) {
            return 'قيمة ' + label + ' لازم تكون رقمًا صحيحًا بين ' + min + ' و ' + max + '.';
        }
    }

    if (field.dataset.pattern === 'letters' && !/^[\p{L}\s]+$/u.test(value)) {
        return label + ' لازم يحتوي على حروف فقط.';
    }

    return '';
}

function showFieldError(field, message) {
    const group = field.closest('.form-group, .hero-search');
    const errorBox = group ? group.querySelector('.error-msg') : null;

    field.classList.toggle('invalid', message !== '');
    if (message) {
        field.setAttribute('aria-invalid', 'true');
    } else {
        field.removeAttribute('aria-invalid');
    }
    if (errorBox) errorBox.textContent = message;
}

function validateForm(form) {
    const fields = form.querySelectorAll('input:not([type=hidden]), select, textarea');
    let firstInvalid = null;

    fields.forEach(function (field) {
        if (field.disabled || field.dataset.noValidate !== undefined) return;
        const message = getFieldError(field);
        showFieldError(field, message);
        if (message && !firstInvalid) firstInvalid = field;
    });

    if (firstInvalid) firstInvalid.focus();
    return firstInvalid === null;
}

function initFormValidation() {
    // كل نموذج عليه novalidate نتحقق منه بالجافاسكربت
    const forms = document.querySelectorAll('form[novalidate]');

    forms.forEach(function (form) {
        let submitted = false;

        form.addEventListener('submit', function (event) {
            submitted = true;
            if (!validateForm(form)) {
                event.preventDefault();
            }
        });

        // بعد أول محاولة إرسال، نحدّث رسالة الحقل وقت ما المستخدم يعدّل
        form.addEventListener('input', function (event) {
            if (submitted) showFieldError(event.target, getFieldError(event.target));
        });
        form.addEventListener('change', function (event) {
            if (submitted || event.target.type === 'file') {
                showFieldError(event.target, getFieldError(event.target));
            }
        });
    });
}

/* ---------- 6. تأكيد الحذف بـ confirm() ----------
   أي نموذج فيه data-confirm يطلع رسالة تأكيد قبل الإرسال */
function initDeleteConfirm() {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
}

/* ---------- 7. إضافات ---------- */

// عدّاد الأرقام في واجهة الصفحة الرئيسية
function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!counters.length || reduceMotion) return;

    counters.forEach(function (el) {
        const target = Number(el.dataset.count);
        const duration = 900;
        const start = performance.now();

        function tick(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(target * eased);
            if (progress < 1) requestAnimationFrame(tick);
        }
        el.textContent = '0';
        requestAnimationFrame(tick);
    });
}

// عدّاد الأحرف تحت أي textarea فيها maxlength
function initCharCounters() {
    document.querySelectorAll('textarea[maxlength]').forEach(function (textarea) {
        const max = Number(textarea.getAttribute('maxlength'));
        const counter = document.createElement('span');
        counter.className = 'char-counter';
        textarea.insertAdjacentElement('afterend', counter);

        function update() {
            counter.textContent = textarea.value.length + ' / ' + max;
            counter.classList.toggle('near-limit', textarea.value.length > max * 0.9);
        }
        textarea.addEventListener('input', update);
        update();
    });
}

// معاينة الصورة قبل الرفع (نموذج إضافة/تعديل المكان)
function initImagePreview() {
    document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
        const preview = document.getElementById(input.dataset.preview);
        if (!preview) return;

        const fileName = input.parentElement.querySelector('.file-name');

        input.addEventListener('change', function () {
            const file = input.files[0];
            if (fileName) fileName.textContent = file ? file.name : fileName.dataset.empty;
            if (!file || !file.type.startsWith('image/')) return;

            let img = preview.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                preview.innerHTML = '';
                preview.appendChild(img);
            }
            img.src = URL.createObjectURL(file);
            img.alt = 'معاينة الصورة الجديدة';
            preview.classList.add('has-image');
        });
    });
}

// زر إظهار/إخفاء كلمة المرور
function initPasswordToggle() {
    document.querySelectorAll('.toggle-password').forEach(function (button) {
        const input = document.getElementById(button.dataset.target);
        if (!input) return;

        button.addEventListener('click', function () {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.setAttribute('aria-pressed', show ? 'true' : 'false');
            button.setAttribute('aria-label', show ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
            button.classList.toggle('showing', show);
        });
    });
}

// زر إغلاق التنبيهات
function initAlerts() {
    document.querySelectorAll('.alert .alert-close').forEach(function (button) {
        button.addEventListener('click', function () {
            const alert = button.closest('.alert');
            alert.classList.add('closing');
            setTimeout(function () { alert.remove(); }, 250);
        });
    });
}

// البحث في جداول لوحة التحكم
function initTableSearch() {
    document.querySelectorAll('[data-table-search]').forEach(function (input) {
        const table = document.getElementById(input.dataset.tableSearch);
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr:not(.empty-row)');
        const emptyRow = table.querySelector('.no-match-row');

        input.addEventListener('input', function () {
            const query = normalizeArabic(input.value);
            let visible = 0;
            rows.forEach(function (row) {
                if (row === emptyRow) return;
                const show = normalizeArabic(row.textContent).includes(query);
                row.hidden = !show;
                if (show) visible++;
            });
            if (emptyRow) emptyRow.hidden = visible > 0;
        });
    });
}

/* ---------- تشغيل كل شي بعد تحميل الصفحة ---------- */
document.addEventListener('DOMContentLoaded', function () {
    initThemeToggle();
    initStationFilters();
    initPlaceFilters();
    initFormValidation();
    initDeleteConfirm();
    initCounters();
    initCharCounters();
    initImagePreview();
    initPasswordToggle();
    initAlerts();
    initTableSearch();
});
