<?php
// 📄 admin/edit.php — واجهة احترافية موحدة لتعديل المواد، المجموعات، والمواضيع
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');

$response = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $id = intval($_POST['id'] ?? 0);
  $name = trim($_POST['name'] ?? '');

  if ($id > 0 && $name !== '') {
    if ($action === 'update_material') {
      $stmt = $conn->prepare("UPDATE materials SET subject = ? WHERE id = ?");
    } elseif ($action === 'update_group') {
      $stmt = $conn->prepare("UPDATE groups SET title = ? WHERE id = ?");
    } elseif ($action === 'update_thread') {
      $stmt = $conn->prepare("UPDATE threads SET title = ? WHERE id = ?");
    }
    if (isset($stmt)) {
      $stmt->bind_param("si", $name, $id);
      $response = $stmt->execute() ? "✅ تم التعديل بنجاح" : "❌ حدث خطأ أثناء التعديل";
    }
  } else {
    $response = "🚫 يرجى تحديد عنصر وإدخال اسم جديد";
  }
}

$classes = $conn->query("SELECT id, name FROM classes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تعديل المحتوى</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { font-family: 'Cairo', sans-serif; }
    .sidebar { background: #f8f9fa; height: 100vh; padding-top: 20px; border-left: 1px solid #ccc; }
    .sidebar button { width: 100%; margin-bottom: 10px; }
    .editor-section { display: none; }
    .editor-section.active { display: block; }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3 sidebar">
      <button class="btn btn-outline-primary" onclick="showEditor('material')">تعديل اسم المادة</button>
      <button class="btn btn-outline-success" onclick="showEditor('group')">تعديل اسم المجموعة</button>
      <button class="btn btn-outline-danger" onclick="showEditor('thread')">تعديل عنوان الموضوع</button>
    </div>

    <div class="col-md-9 py-4">
      <?php if ($response): ?>
        <div class="alert alert-info"> <?= $response ?> </div>
      <?php endif; ?>

      <!-- ✅ تعديل اسم المادة -->
      <div id="edit-material" class="editor-section active">
        <h5>✏️ تعديل اسم مادة</h5>
        <form method="POST">
          <input type="hidden" name="action" value="update_material">
          <div class="mb-2">
            <label>اختر الصف</label>
            <select name="class_id" id="material_class_select" class="form-select" required>
              <option value="">-- اختر الصف --</option>
              <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2">
            <label>اختر المادة</label>
            <select name="id" id="material_select" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label>الاسم الجديد</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <button class="btn btn-primary">💾 تعديل</button>
        </form>
      </div>

      <!-- ✅ تعديل اسم المجموعة -->
      <div id="edit-group" class="editor-section">
        <h5>✏️ تعديل اسم مجموعة</h5>
        <form method="POST">
          <input type="hidden" name="action" value="update_group">
          <div class="mb-2">
            <label>اختر الصف</label>
            <select id="group_class_select" class="form-select" required>
              <option value="">-- اختر الصف --</option>
              <?php mysqli_data_seek($classes, 0); foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2">
            <label>اختر المادة</label>
            <select id="group_material_select" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label>اختر الفصل</label>
            <select id="group_semester_select" class="form-select" required>
              <option value="0">الفصل الأول</option>
              <option value="1">الفصل الثاني</option>
            </select>
          </div>
          <div class="mb-2">
            <label>اختر المجموعة</label>
            <select name="id" id="group_select" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label>الاسم الجديد</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <button class="btn btn-success">💾 تعديل</button>
        </form>
      </div>

      <!-- ✅ تعديل عنوان الموضوع -->
      <div id="edit-thread" class="editor-section">
        <h5>✏️ تعديل عنوان موضوع</h5>
        <form method="POST">
          <input type="hidden" name="action" value="update_thread">
          <div class="mb-2">
            <label>اختر الصف</label>
            <select id="thread_class_select" class="form-select" required>
              <option value="">-- اختر الصف --</option>
              <?php mysqli_data_seek($classes, 0); foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2">
            <label>اختر المادة</label>
            <select id="thread_material_select" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label>اختر الفصل</label>
            <select id="thread_semester_select" class="form-select" required>
              <option value="0">الفصل الأول</option>
              <option value="1">الفصل الثاني</option>
            </select>
          </div>
          <div class="mb-2">
            <label>اختر المجموعة</label>
            <select id="thread_group_select" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label>اختر الموضوع</label>
            <select name="id" id="thread_select" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label>العنوان الجديد</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <button class="btn btn-danger">💾 تعديل</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function showEditor(type) {
  document.querySelectorAll('.editor-section').forEach(div => div.classList.remove('active'));
  document.getElementById('edit-' + type).classList.add('active');
}

$("#material_class_select, #group_class_select, #thread_class_select").change(function () {
  const classId = $(this).val();
  const target = $(this).attr('id');
  let materialSelect = "";

  if (target === "material_class_select") {
    materialSelect = "#material_select";
  } else if (target === "group_class_select") {
    materialSelect = "#group_material_select";
  } else if (target === "thread_class_select") {
    materialSelect = "#thread_material_select";
  }

  if (!classId || materialSelect === "") return;

  $.get("materials.php", { id: classId }, function (data) {
    try {
      const list = JSON.parse(data);
      let options = '<option value="">-- اختر المادة --</option>';
      list.forEach(row => {
        options += `<option value="${row.id}">${row.subject}</option>`;
      });
      $(materialSelect).html(options);
    } catch (e) {
      console.error("🛑 فشل تحميل المواد:", e);
      console.log("البيانات المستلمة:", data);
    }
  });
});

$("#group_material_select, #thread_material_select, #group_semester_select, #thread_semester_select").change(function () {
  const mat = $(this).attr('id').includes('thread') ? '#thread_' : '#group_';
  const mid = $(mat + 'material_select').val();
  const semester = $(mat + 'semester_select').val();
  if (mid && semester !== undefined) {
    $.get("groups.php", { id: mid, semester: semester }, function (data) {
      const list = JSON.parse(data);
      let options = '<option value="">-- اختر المجموعة --</option>';
      list.forEach(row => {
        options += `<option value="${row.id}">${row.title}</option>`;
      });
      $(mat + 'group_select').html(options);
    });
  }
});

$("#thread_group_select").change(function () {
  const gid = $(this).val();
  if (gid) {
    $.get("threads.php", { id: gid }, function (data) {
      const list = JSON.parse(data);
      let options = '<option value="">-- اختر الموضوع --</option>';
      list.forEach(row => {
        options += `<option value="${row.id}">${row.title}</option>`;
      });
      $("#thread_select").html(options);
    });
  }
});
</script>
</body>
</html>