<?php
// 📄 admin/add_thread.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');

$success = isset($_GET['success']);
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'] ?? '';
  $description = $_POST['description'] ?? '';
  $class_id = $_POST['class_id'] ?? '';
  $material_id = $_POST['material_id'] ?? '';
  $semester_id = $_POST['semester_id'] ?? '';
  $group_id = $_POST['group_id'] ?? '';
  $type = $_POST['type'] ?? '';

  $stmt = $conn->prepare("INSERT INTO threads (title, description, class_id, material_id, semester_id, group_id, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssiiiis", $title, $description, $class_id, $material_id, $semester_id, $group_id, $type);

  if ($stmt->execute()) {
    $thread_id = $stmt->insert_id;

    // رابط خارجي
    if ($type === 'link' && !empty($_POST['external_link'])) {
      $link = trim($_POST['external_link']);
      if (filter_var($link, FILTER_VALIDATE_URL)) {
        $link_name = basename(parse_url($link, PHP_URL_PATH));
        $stmt2 = $conn->prepare("INSERT INTO attachments (thread_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $thread_id, $link_name, $link, $type);
        $stmt2->execute();
        $stmt2->close();
      }
    }
    // ملفات محلية
    elseif (!empty($_FILES['files']['name'][0])) {
      foreach ($_FILES['files']['name'] as $index => $name) {
        $tmp_name = $_FILES['files']['tmp_name'][$index];
        $unique = time() . "_$index" . "_" . basename($name);
        $target = "../public/uploads/$unique";
        if (move_uploaded_file($tmp_name, $target)) {
          $mime = mime_content_type($target);
          $path = "public/uploads/$unique";
          $stmt3 = $conn->prepare("INSERT INTO attachments (thread_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
          $stmt3->bind_param("isss", $thread_id, $name, $path, $mime);
          $stmt3->execute();
          $stmt3->close();
        }
      }
    }

    header('Location: add_thread.php?success=1');
    exit;
  } else {
    $message = "<div class='alert alert-danger text-center'>❌ حدث خطأ أثناء الإضافة</div>";
  }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة موضوع جديد</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f7f9fb; padding-top: 80px; }
    header, footer {
      background: linear-gradient(to right, #1e3a8a, #0f172a);
      color: white;
      padding: 10px 20px;
      position: fixed;
      width: 100%;
      z-index: 1000;
    }
    header { top: 0; }
    footer { bottom: 0; text-align: center; }
    aside.sidebar {
      position: fixed; right: 0; top: 80px; height: calc(100vh - 160px);
      width: 230px; background: #1e3a8a; color: white; padding: 20px;
      overflow-y: auto;
    }
    .main-content { margin-right: 250px; padding: 30px; margin-bottom: 80px; }
    .form-label { font-weight: bold; }
    .sidebar h5 { margin-top: 30px; font-size: 16px; border-bottom: 1px solid white; padding-bottom: 5px; }
  </style>
</head>
<body>

<header>
  <h4>📘 المنصة التعليمية - إضافة موضوع</h4>
</header>

<aside class="sidebar">
  <a href="https://www.shneler.com/azmi2/public/index.html" class="text-white d-block mb-2">🏠 الرئيسية</a>
<a href="edit.php" class="text-white d-block mb-3">✏️ تعديل المواضيع والمواد</a>

  <h5>➕ مادة جديدة</h5>
  <form onsubmit="addMaterial(event)">
    <select id="material-class" class="form-select mb-2" required></select>
    <input type="text" id="material-name" class="form-control mb-2" placeholder="اسم المادة" required>
    <button class="btn btn-light w-100">إضافة</button>
  </form>

  <h5>➕ مجموعة جديدة</h5>
  <form onsubmit="addGroup(event)">
    <select id="group-class" class="form-select mb-2" required onchange="loadGroupMaterials()"></select>
    <select id="group-material" class="form-select mb-2" required></select>
    <select id="group-semester" class="form-select mb-2" required>
      <option value="0">الفصل الأول</option>
      <option value="1">الفصل الثاني</option>
    </select>
    <input type="text" id="group-name" class="form-control mb-2" placeholder="اسم المجموعة" required>
    <button class="btn btn-light w-100">إضافة</button>
  </form>
</aside>

<div class="main-content">
  <h2 class="text-blue-800">📋 إضافة موضوع جديد</h2>
  <?= $success ? "<div class='alert alert-success'>✅ تم إضافة الموضوع</div>" : '' ?>
  <?= $message ?>

  <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow border mt-3">
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">العنوان</label>
        <input type="text" name="title" required class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">نوع المحتوى</label>
        <select name="type" id="type" class="form-select" required>
          <option value="pdf">📄 PDF</option>
          <option value="image">🖼️ صورة</option>
          <option value="video">🎥 فيديو</option>
          <option value="doc">📄 Word</option>
          <option value="link">🔗 رابط خارجي</option>
        </select>
      </div>
    </div>

    <div id="external-link-box" style="display:none;" class="mb-3">
      <label class="form-label">الرابط الخارجي:</label>
      <input type="url" name="external_link" class="form-control">
    </div>

    <div id="upload-box" class="mb-3">
      <label class="form-label">رفع الملفات:</label>
      <input type="file" name="files[]" class="form-control" multiple>
    </div>

    <div class="mb-3">
      <label class="form-label">الوصف:</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>

    <div class="row mb-3">
      <div class="col-md-3">
        <label class="form-label">الصف</label>
        <select name="class_id" id="class_id" class="form-select" required onchange="fetchMaterials()"></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">المادة</label>
        <select name="material_id" id="material_id" class="form-select" required onchange="fetchGroups()"></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">الفصل</label>
        <select name="semester_id" id="semester_id" class="form-select" required onchange="fetchGroups()"></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">المجموعة</label>
        <select name="group_id" id="group_id" class="form-select" required></select>
      </div>
    </div>

    <button class="btn btn-primary">➕ إضافة الموضوع</button>
  </form>
</div>

<footer>جميع الحقوق محفوظة © المنصة التعليمية</footer>



<script>
document.getElementById("type").addEventListener("change", e => {
  const isLink = e.target.value === "link";
  document.getElementById("external-link-box").style.display = isLink ? "block" : "none";
  document.getElementById("upload-box").style.display = isLink ? "none" : "block";
});

function fetchSelects() {
  fetch("../api/classes.php").then(r => r.json()).then(data => {
    const all = ["class_id", "material-class", "group-class"];
    all.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      el.innerHTML = "<option value=''>اختر الصف</option>";
      data.forEach(c => el.innerHTML += `<option value='${c.id}'>${c.name}</option>`);
    });
  });

  fetch("../api/semesters.php").then(r => r.json()).then(data => {
    const sem = document.getElementById("semester_id");
    if (sem) {
      sem.innerHTML = "<option value=''>اختر الفصل</option>";
      data.forEach(s => sem.innerHTML += `<option value='${s.id}'>${s.name}</option>`);
    }

    const groupSem = document.getElementById("group-semester");
    if (groupSem) {
      groupSem.innerHTML = "<option value=''>اختر الفصل</option>";
      data.forEach(s => groupSem.innerHTML += `<option value='${s.id}'>${s.name}</option>`);
    }
  });
}

// قائمة المواد بحسب الصف الرئيسي
function fetchMaterials() {
  const classId = document.getElementById("class_id").value;
  fetch(`../api/materials.php?class_id=${classId}`).then(r => r.json()).then(data => {
    const el = document.getElementById("material_id");
    el.innerHTML = "<option value=''>اختر المادة</option>";
    data.forEach(m => el.innerHTML += `<option value='${m.id}'>${m.name}</option>`);
  });
}

// جلب المجموعات حسب المادة والفصل
function fetchGroups() {
  const mat = document.getElementById("material_id").value;
  const sem = document.getElementById("semester_id").value;
  fetch(`../api/groups.php?material_id=${mat}&semester_id=${sem}`).then(r => r.json()).then(data => {
    const el = document.getElementById("group_id");
    el.innerHTML = "<option value=''>اختر المجموعة</option>";
    data.forEach(g => el.innerHTML += `<option value='${g.id}'>${g.name}</option>`);
  });
}

// قائمة المواد في قسم إضافة مجموعة بناءً على الصف المحدد
function loadGroupMaterials() {
  const classId = document.getElementById("group-class").value;
  fetch(`../api/materials.php?class_id=${classId}`).then(r => r.json()).then(data => {
    const mat = document.getElementById("group-material");
    mat.innerHTML = "<option value=''>اختر المادة</option>";
    data.forEach(m => mat.innerHTML += `<option value='${m.id}'>${m.name}</option>`);
  });
}

// إضافة مادة جديدة
function addMaterial(e) {
  e.preventDefault();
  const classId = document.getElementById("material-class").value;
  const name = document.getElementById("material-name").value;
  fetch("ajax_add_material.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `class_id=${classId}&name=${encodeURIComponent(name)}`
  }).then(res => res.json()).then(data => {
    alert(data.message);
    if (data.status === 'success') {
      fetchSelects(); // تحديث القوائم
      document.getElementById("material-name").value = '';
    }
  });
}

// إضافة مجموعة جديدة
function addGroup(e) {
  e.preventDefault();
  const classId = document.getElementById("group-class").value;
  const materialId = document.getElementById("group-material").value;
  const semesterId = document.getElementById("group-semester").value;
  const name = document.getElementById("group-name").value;
  fetch("ajax_add_group.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `class_id=${classId}&material_id=${materialId}&semester_id=${semesterId}&name=${encodeURIComponent(name)}`
  }).then(res => res.json()).then(data => {
    alert(data.message);
    if (data.status === 'success') {
      fetchGroups(); // تحديث المجموعات بعد الإضافة
      document.getElementById("group-name").value = '';
    }
  });
}

window.onload = fetchSelects;
</script>



</body>
</html>