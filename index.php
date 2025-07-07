<?php
require_once "includes/db.php";

// جلب المواد من قاعدة البيانات (ليس ضروري هنا، لكن موجود حسب كودك)
$stmt = $conn->prepare("SELECT * FROM materials");
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>لوحة التحكم - منصة عزمي</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --header-height: 56px;
            --footer-height: 44px;
            --sidebar-width-desktop: 230px;
            --sidebar-width-mobile: 220px;
            --main-bg-start: #f3f5fa;
            --main-bg-end: #e2e6f2;
            --header-bg: #19202e;
            --sidebar-bg: #222e3c;
            --sidebar-link-hover-start: #3366ff;
            --sidebar-link-hover-end: #2b394c;
            --border-color: #33425b;
            --text-color-light: #fff;
            --shadow-light: rgba(34,46,60,0.08);
            --shadow-medium: rgba(34,46,60,0.07);
            --shadow-dark: rgba(34,46,60,0.11);
            --blue-shadow: rgba(51,102,255,0.08);
        }

        html, body {
            height: 100%; /* تأكد أن HTML و BODY يملآن الارتفاع بالكامل */
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* **مهم جداً: منع التمرير الأفقي على مستوى HTML و Body** */
            box-sizing: border-box; /* لضمان احتساب البادينج والبوردير ضمن العرض */
        }

        body {
            background: linear-gradient(135deg, var(--main-bg-start) 0%, var(--main-bg-end) 100%);
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        .fixed-header, .fixed-footer {
            position: sticky; /* استخدم sticky لتحسين الأداء وتجنب مشاكل fixed */
            width: 100%;
            z-index: 1040;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            box-shadow: 0 2px 6px var(--shadow-light);
            flex-shrink: 0; /* منع الهيدر والفوتر من الانكماش */
        }

        .fixed-header {
            top: 0;
            background: var(--header-bg);
            color: var(--text-color-light);
            height: var(--header-height);
            justify-content: space-between;
        }

        .fixed-footer {
            bottom: 0;
            background: var(--sidebar-bg);
            color: var(--text-color-light);
            height: var(--footer-height);
            justify-content: center;
            font-size: 1rem;
            letter-spacing: 1px;
            box-shadow: 0 -2px 8px var(--shadow-light);
            margin-top: auto; /* يدفع الفوتر للأسفل عند استخدام flex-direction: column على body */
        }

        /* هذا هو العنصر الذي سيحتوي السايدبار والمحتوى الرئيسي */
        .content-wrapper {
            display: flex;
            flex-grow: 1; /* يجعل هذا العنصر يملأ المساحة المتبقية بين الهيدر والفوتر */
            /* لا داعي لـ overflow: hidden هنا إذا كان body يتعامل معه بشكل صحيح */
        }

        .sidebar {
            background: var(--sidebar-bg);
            color: var(--text-color-light);
            min-width: var(--sidebar-width-desktop);
            max-width: var(--sidebar-width-desktop);
            position: sticky; /* شريط جانبي ثابت على الأجهزة الكبيرة */
            top: var(--header-height); /* أسفل الهيدر مباشرة */
            height: calc(100vh - var(--header-height) - var(--footer-height)); /* ارتفاع محسوب */
            padding-top: 1.5rem;
            overflow-y: auto; /* تمكين التمرير إذا كان المحتوى أطول من المساحة المتاحة */
            z-index: 1030;
            box-shadow: 0 4px 24px var(--shadow-medium);
            transition: all .25s cubic-bezier(.25,.8,.25,1); /* انتقال سلس للحركات */
            flex-shrink: 0; /* منع السايدبار من الانكماش على الشاشات الكبيرة */
        }

        .sidebar .nav-link {
            color: var(--text-color-light);
            margin-bottom: 0.5rem;
            border-radius: 8px;
            font-size: 1.06rem;
            padding: 0.625rem 1.375rem;
            transition: background .15s, box-shadow .15s;
            font-weight: 600;
            letter-spacing: .5px;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: linear-gradient(90deg, var(--sidebar-link-hover-start) 0%, var(--sidebar-link-hover-end) 100%);
            color: var(--text-color-light);
            box-shadow: 0 2px 6px var(--blue-shadow);
        }

        .sidebar-header {
            padding: 0.75rem 1.5rem 1.125rem 1.5rem;
            font-size: 1.18rem;
            font-weight: 700;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .sidebar-toggler {
            display: none;
            font-size: 1.4rem;
            background: none;
            border: none;
            color: var(--text-color-light);
            margin-left: 0.5rem;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .main-content {
            padding: 2.375rem 1.375rem 1.375rem 1.375rem;
            flex-grow: 1;
            box-sizing: border-box;
            overflow-y: auto; /* المحتوى الرئيسي نفسه قابل للتمرير عمودياً */
        }

        iframe#mainframe {
            width: 100%;
            height: 100%; /* اجعل الـ iframe يملأ ارتفاع الـ main-content */
            min-height: 70vh; /* احتفظ بهذا كحد أدنى مرئي في حال المحتوى قليل */
            border: none;
            background: var(--text-color-light);
            border-radius: 14px;
            box-shadow: 0 8px 32px var(--shadow-light);
            transition: box-shadow .15s;
        }

        /* تحسينات أيقونات السايدبار */
        .sidebar .nav-link i {
            margin-left: 0.5rem;
            font-size: 1.2em;
            vertical-align: middle;
        }

        /* زر إغلاق السايدبار للموبايل */
        .close-sidebar-btn {
            display: none;
            position: absolute;
            left: 0.625rem;
            top: 0.75rem;
            font-size: 1.7em;
            color: var(--text-color-light);
            background: none;
            border: none;
            z-index: 1100;
            cursor: pointer;
        }

        /* Overlay للخلفية عند فتح السايدبار في وضع الجوال */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1045;
            display: none;
            transition: opacity .25s ease-in-out;
            opacity: 0;
        }
        .overlay.show {
            display: block;
            opacity: 1;
        }

        /* Responsive Breakpoints */
        /* Tablet & Small Desktop (992px - 768px) */
        @media (max-width: 991.98px) {
            .sidebar {
                min-width: 70px;
                max-width: 70px;
                padding-top: 1rem;
            }
            .sidebar .nav-link {
                font-size: 0.9rem;
                padding: 0.625rem 0.5rem;
                justify-content: center;
                text-align: center;
            }
            .sidebar .nav-link span {
                display: none;
            }
            .sidebar .nav-link i {
                margin: 0;
            }
            .sidebar-header, .sidebar hr {
                display: none;
            }
            .main-content {
                padding: 2rem 1rem 1rem 1rem;
            }
            .fixed-header > div:last-child .btn-sm {
                 font-size: 0.75rem;
                 padding: .25rem .5rem;
            }
        }

        /* Mobile (أقل من 768px) */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                right: calc(-1 * var(--sidebar-width-mobile));
                top: 0;
                bottom: 0;
                height: 100vh;
                border-radius: 0 0 0 16px;
                box-shadow: 4px 0 24px var(--shadow-dark);
                z-index: 1050;
                max-width: var(--sidebar-width-mobile);
                min-width: var(--sidebar-width-mobile);
                padding-top: 4rem;
            }
            .sidebar.show {
                right: 0;
            }
            .sidebar-header {
                display: block;
                padding-top: 1.5rem;
                text-align: center;
            }
            .sidebar hr {
                display: block;
            }
            .sidebar .nav-link {
                justify-content: flex-start;
                text-align: right;
                font-size: 1.06rem;
                padding: 0.625rem 1.375rem;
            }
            .sidebar .nav-link span {
                display: inline-block;
            }
            .sidebar .nav-link i {
                margin-left: 0.5rem;
            }

            .sidebar-toggler {
                display: inline-block;
            }
            .main-content {
                margin-right: 0;
                padding: 1rem 0.5rem 0.5rem 0.5rem;
                padding-top: calc(var(--header-height) + 1rem);
                padding-bottom: calc(var(--footer-height) + 1rem);
            }
            .fixed-header {
                padding: 0 0.75rem;
                position: fixed; /* استخدام fixed هنا للثبات التام على الجوال */
            }
            .fixed-footer {
                position: fixed; /* استخدام fixed هنا للثبات التام على الجوال */
            }
            .close-sidebar-btn {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="fixed-header">
        <div class="d-flex align-items-center">
            <button class="sidebar-toggler" id="sidebarToggle" aria-label="فتح القائمة">
                <i class="bi bi-list"></i> </button>
            <b style="letter-spacing:.5px;">لوحة الإدارة</b>
        </div>
        <div>
            <a href="#" onclick="location.reload();" class="btn btn-light btn-sm ms-2">إعادة تحميل الصفحة</a>
            <a href="../index.php" class="btn btn-primary btn-sm" target="_blank">الذهاب للواجهة الأمامية</a>
        </div>
    </div>

    <div class="overlay" id="sidebarOverlay"></div>

    <div class="content-wrapper">
        <div class="sidebar" id="sidebar">
            <button class="close-sidebar-btn" id="closeSidebarBtn" aria-label="إغلاق القائمة">
                <i class="bi bi-x-lg"></i> </button>
            <div class="sidebar-header">القائمة الرئيسية</div>
            <hr style="background:var(--border-color);">
            <nav class="nav flex-column px-3"> <a href="add_material.php" target="mainframe" class="nav-link"><i class="bi bi-book"></i> <span>إضافة مادة</span></a>
                <a href="groups.php" target="mainframe" class="nav-link"><i class="bi bi-collection"></i> <span>إضافة مجموعة</span></a>
                <a href="contents.php" target="mainframe" class="nav-link"><i class="bi bi-file-earmark-text"></i> <span>إضافة محتوى</span></a>
                <a href="threads.php" target="mainframe" class="nav-link"><i class="bi bi-chat-left-dots"></i> <span>اضافة موضوع</span></a>
                <a href="settings.php" target="mainframe" class="nav-link"><i class="bi bi-gear"></i> <span>الإعدادات</span></a>
            </nav>
        </div>

        <div class="main-content">
            <iframe name="mainframe" id="mainframe"></iframe>
        </div>
    </div>

    <div class="fixed-footer">
        جميع الحقوق محفوظة &copy; <?= date('Y'); ?> منصة عزمي التعليمية
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Function to toggle sidebar visibility and overlay
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Open sidebar on mobile via header button
        document.getElementById('sidebarToggle').onclick = toggleSidebar;

        // Close sidebar on mobile via close button inside sidebar
        document.getElementById('closeSidebarBtn').onclick = toggleSidebar;

        // Close sidebar when clicking on the overlay
        document.getElementById('sidebarOverlay').onclick = toggleSidebar;

        // Function to activate navigation link
        function activateNavLink(href) {
            document.querySelectorAll('.sidebar .nav-link').forEach(function(a){
                a.classList.remove('active');
            });
            const activeLink = document.querySelector(`.sidebar .nav-link[href="${href}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        // Initial load: Set iframe source and activate nav link
        window.onload = function(){
            const initialPage = "threads.php"; // الصفحة الافتراضية
            let currentHash = window.location.hash.substring(1); // إزالة #
            let targetSrc = initialPage;

            // إذا كان هناك hash في الـ URL، حاول مطابقته بصفحة في السايدبار
            if (currentHash) {
                const navLinks = document.querySelectorAll('.sidebar .nav-link');
                for (let i = 0; i < navLinks.length; i++) {
                    const linkHref = navLinks[i].getAttribute('href').split('.')[0]; // خذ اسم الملف بدون الامتداد
                    if (linkHref === currentHash) {
                        targetSrc = navLinks[i].getAttribute('href');
                        break;
                    }
                }
            }

            document.getElementById('mainframe').src = targetSrc;
            activateNavLink(targetSrc);
        };

        // On sidebar link click: activate link and close sidebar on mobile
        document.querySelectorAll('.sidebar .nav-link').forEach(function(a){
            a.onclick = function(){
                const linkHref = this.getAttribute('href');
                activateNavLink(linkHref);

                // إغلاق السايدبار عند الضغط على رابط في الجوال
                if(window.innerWidth <= 767.98) { // استخدم نفس نقطة توقف الـ CSS
                    toggleSidebar();
                }
            };
        });

        // Update active link when iframe content changes (e.g., if page navigates internally)
        document.getElementById('mainframe').addEventListener('load', function() {
            try {
                // محاولة الحصول على مسار الصفحة داخل الـ iframe
                // قد تفشل بسبب سياسات CORS إذا كانت محتويات الـ iframe من نطاق مختلف
                var src = this.contentWindow.location.pathname.split('/').pop();
                activateNavLink(src);
            } catch (e) {
                // إذا فشلت محاولة الوصول إلى محتوى iframe بسبب CORS، يمكن التعامل معها هنا
                console.warn("Could not determine iframe source due to cross-origin policy.", e);
            }
        });
    </script>
</body>
</html>