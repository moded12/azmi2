<?php
$conn = new mysqli('localhost', 'azmi3', 'Tvvcrtv1610@', 'azmi3');
$conn->set_charset('utf8mb4');

// جلب الصفوف
$classes = [];
$q = $conn->query("SELECT DISTINCT TRIM(class_id) AS class_id FROM materials WHERE class_id IS NOT NULL AND class_id != '' ORDER BY LENGTH(class_id), class_id");
while($row = $q->fetch_assoc()) {
    if($row['class_id'] !== '') $classes[] = $row['class_id'];
}

// جلب المواد مع الصف المرتبط بها
$materials = [];
$q = $conn->query("SELECT id, name, class_id FROM materials ORDER BY name");
while($row = $q->fetch_assoc()) $materials[] = $row;

// جلب الفصول
$semesters = [];
$q = $conn->query("SELECT DISTINCT name FROM semesters ORDER BY id");
while($row = $q->fetch_assoc()) $semesters[] = $row['name'];

// جلب المجموعات
$groups = [];
$q = $conn->query("SELECT id, name FROM groups ORDER BY id");
while($row = $q->fetch_assoc()) $groups[] = $row;

// قائمة الأيقونات التعليمية (Bootstrap Icons)
$edu_icons = [
    "bi-mortarboard",
    "bi-book",
    "bi-bookmark-star",
    "bi-journal-text",
    "bi-lightbulb",
    "bi-pencil",
    "bi-easel",
    "bi-globe",
    "bi-award",
    "bi-calculator"
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>المنصة التعليمية - مجلة المحتوى</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  
  <link rel="stylesheet" href="/azmi3/public/css/style.css">

</head>

<body class="bg-[#f7fbfd]">
  <!-- Header -->
  <header class="main-header shadow-md">
    <div class="overlay"></div>
    <nav class="navbar navbar-expand-lg navbar-magazine px-3">
      <div class="container flex items-center justify-between">
        <a class="navbar-brand flex items-center gap-2 text-xl md:text-2xl font-bold" href="#" style="color:#fff;">
          <i class="bi bi-journal-richtext"></i> <span>المنصة التعليمية</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="تبديل القائمة">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="mainNav">
          <ul class="navbar-nav mb-2 mb-lg-0 gap-2 flex items-center text-white text-xl space-x-3">
            <li class="nav-item">
              <a class="nav-link active hover:underline transition-all duration-150" href="#">
                <i class="bi bi-house-door"></i> الرئيسية
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link hover:underline transition-all duration-150" href="#latest">
                <i class="bi bi-lightning"></i> آخر المواضيع
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link hover:underline transition-all duration-150" href="#contact">
                <i class="bi bi-envelope"></i> اتصل بنا
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>


<div class="main-header-content">
  <h1 class="inline-block">
    <a 
      href="javascript:location.reload()" 
      class="text-white cursor-pointer no-underline"
      title="إعادة تحميل الصفحة"
    >
      <i class="bi bi-journal-richtext"></i> المنصة التعليمية
    </a>
  </h1>
  <p>مجلة المحتوى التعليمية – سلطنة عمان 2025</p>
</div>





  </header>
  <!-- End Header -->

  <!-- مربع البحث العصري -->
  <form class="search-bar-magazine position-relative" onsubmit="event.preventDefault(); loadThreads();">
    <select id="classFilter" class="form-select w-auto focus:ring-2 focus:ring-[#1c3d5e]">
      <option value="">اختر صف</option>
      <?php foreach($classes as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
      <?php endforeach; ?>
    </select>
    <select id="materialFilter" class="form-select w-auto focus:ring-2 focus:ring-[#1c3d5e]" disabled>
      <option value="">اختر الصف أولاً</option>
    </select>
    <select id="semesterFilter" class="form-select w-auto focus:ring-2 focus:ring-[#1c3d5e]">
      <option value="">اختر فصل</option>
      <?php foreach($semesters as $s): ?>
        <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
      <?php endforeach; ?>
    </select>
    <select id="groupFilter" class="form-select w-auto focus:ring-2 focus:ring-[#1c3d5e]">
      <option value="">اختر مجموعة</option>
      <?php foreach($groups as $g): ?>
        <option value="<?php echo htmlspecialchars($g['name']); ?>"><?php echo htmlspecialchars($g['name']); ?></option>
      <?php endforeach; ?>
    </select>
    <div style="position:relative;display:inline-block;">
      <input id="searchInput" class="search-input focus:ring-2 focus:ring-[#1c3d5e]" type="text" placeholder="ابحث عن موضوع أو وصف ..." />
      <span class="search-icon"><i class="bi bi-search"></i></span>
    </div>
  </form>

  <!-- شبكة المجلة -->
  <section class="magazine-grid" id="cardsArea">
    <!-- يتم تعبئتها ديناميكيًا -->
  </section>

  <!-- الفوتر -->
  <footer class="footer mt-16" id="contact">
    جميع الحقوق محفوظة - المنصة التعليمية &copy; 2025<br>
    <span>تواصل معنا: <a href="mailto:info@example.com" class="text-yellow-400 hover:underline">info@example.com</a></span>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    var allMaterials = <?php echo json_encode($materials, JSON_UNESCAPED_UNICODE); ?>;
    var eduIcons = <?php echo json_encode($edu_icons); ?>;

    document.addEventListener('DOMContentLoaded', function() {
      var classSelect = document.getElementById('classFilter');
      var materialSelect = document.getElementById('materialFilter');

      classSelect.addEventListener('change', function() {
        var classId = this.value;
        materialSelect.innerHTML = '';
        if (!classId) {
          materialSelect.innerHTML = '<option value="">اختر الصف أولاً</option>';
          materialSelect.setAttribute('disabled', 'disabled');
        } else {
          var opts = '<option value="">اختر مادة</option>';
          var found = false;
          allMaterials.forEach(function(m) {
            if (m.class_id == classId) {
              opts += `<option value="${m.name}">${m.name}</option>`;
              found = true;
            }
          });
          if (!found) {
            opts += '<option value="" disabled>لا يوجد مواد لهذا الصف</option>';
          }
          materialSelect.innerHTML = opts;
          materialSelect.removeAttribute('disabled');
        }
        loadThreads();
      });

      ['materialFilter','semesterFilter','groupFilter','searchInput'].forEach(function(id){
        document.getElementById(id).addEventListener(
          id==='searchInput' ? 'input' : 'change',
          function(){ loadThreads(); }
        );
      });

      loadThreads();
    });

    function loadThreads() {
      const search = encodeURIComponent(document.getElementById('searchInput').value.trim());
      const material = encodeURIComponent(document.getElementById('materialFilter').value);
      const class_id = encodeURIComponent(document.getElementById('classFilter').value);
      const semester = encodeURIComponent(document.getElementById('semesterFilter').value);
      const group = encodeURIComponent(document.getElementById('groupFilter').value);
      let url = `api.php?action=list_threads&search=${search}&material=${material}&class_id=${class_id}&semester=${semester}&group=${group}`;
      fetch(url)
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            displayThreads(data.data);
          } else {
            document.getElementById('cardsArea').innerHTML = `<div class="alert alert-danger w-100 mt-4">فشل في تحميل البيانات!</div>`;
          }
        })
        .catch(() => {
          document.getElementById('cardsArea').innerHTML = `<div class="alert alert-danger w-100 mt-4">خطأ في الاتصال بالخادم!</div>`;
        });
    }
    function displayThreads(threads) {
      const threadsMap = {};
      threads.forEach(row => {
        if (!threadsMap[row.thread_id]) {
          threadsMap[row.thread_id] = {
            ...row,
            files: []
          };
        }
        if (row.file_path) {
          threadsMap[row.thread_id].files.push("https://www.shneler.com/azmi3/admin/" + row.file_path.replace(/^ *uploads\//, "uploads/"));
        }
      });
      const container = document.getElementById('cardsArea');
      container.innerHTML = '';
      Object.values(threadsMap).forEach((thread, idx) => {
        let iconClass = eduIcons[thread.thread_id % eduIcons.length];
        let avatarHtml = `<span class="avatar-edu"><i class="bi ${iconClass}"></i></span>`;

        // اختيار مصدر الصورة: أولاً thumbnail من قاعدة البيانات، ثم أي مرفق، وأخيراً placeholder
        const placeholder = "https://via.placeholder.com/400x210/1c3d5e/ffffff?text=لا+يوجد+صورة";
        const src = thread.thumbnail || thread.files[0] || placeholder;

        const imgHtml = `
          <div class="magazine-card-img">
            ${avatarHtml}
            <img 
              src="${src}" 
              alt="صورة الموضوع" 
              style="width:100%; height:210px; object-fit:cover;"
            >
          </div>
        `;

        container.innerHTML += `
          <div class="magazine-card group hover:shadow-lg hover:scale-105 transition-all duration-200">
            ${imgHtml}
            <div class="magazine-card-body">
              <a class="magazine-card-title hover:text-yellow-300 transition-colors duration-75" href="view.php?thread_id=${thread.thread_id}">${thread.thread_title}</a>
              <div class="magazine-card-desc">${thread.thread_description || ''}</div>
              <div class="magazine-card-meta flex flex-col gap-1 text-white">
                <span><i class="bi bi-journal-text"></i> المادة: <b>${thread.material_name}</b></span>
                <span><i class="bi bi-book"></i> الصف: <b>${thread.class_id}</b></span>
                <span><i class="bi bi-calendar2-week"></i> الفصل: <b>${thread.semester_name}</b></span>
                <span><i class="bi bi-people"></i> المجموعة: <b>${thread.group_name}</b></span>
              </div>
            </div>
          </div>
        `;
      });
      if (!Object.keys(threadsMap).length) {
        container.innerHTML = `<div class="alert alert-warning w-100 mt-4">لا يوجد مواضيع متاحة حالياً.</div>`;
      }
    }
  </script>
</body>
</html>