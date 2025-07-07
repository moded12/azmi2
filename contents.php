<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "includes/db.php";

// جلب الصفوف (classes)
$classes = $conn->query("SELECT id, name FROM classes")->fetchAll(PDO::FETCH_ASSOC);

// جلب الفصول (semesters)
$semesters = $conn->query("SELECT id, name FROM semesters")->fetchAll(PDO::FETCH_ASSOC);

// جلب المواد لصف معيّن عبر AJAX
if (isset($_GET['get_materials']) && intval($_GET['class_id'])) {
    $class_id = intval($_GET['class_id']);
    $mats = $conn->prepare("SELECT id, name FROM materials WHERE class_id=?");
    $mats->execute([$class_id]);
    echo json_encode($mats->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// جلب المجموعات بناء على المادة والفصل
if (isset($_GET['get_groups']) && intval($_GET['material_id']) && isset($_GET['semester_id'])) {
    $material_id = intval($_GET['material_id']);
    $semester_id = intval($_GET['semester_id']);
    $grps = $conn->prepare("SELECT id, name FROM groups WHERE material_id=? AND semester_id=?");
    $grps->execute([$material_id, $semester_id]);
    echo json_encode($grps->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content_id = intval($_POST['content_id'] ?? 0);

    if (isset($_POST['save'])) {
        $title = trim($_POST['title'] ?? "");
        $class_id = intval($_POST['class_id'] ?? 0);
        $material_id = intval($_POST['material_id'] ?? 0);
        $semester_id = intval($_POST['semester_id'] ?? 0);
        $group_id = intval($_POST['group_id'] ?? 0);

        // تحقق فقط من الحقول المطلوبة بدون body
        if (!$title || !$class_id || !$material_id || !$semester_id || !$group_id) {
            $msg = '<div class="alert alert-danger">جميع الحقول مطلوبة!</div>';
        } else {
            if ($content_id > 0) {
                $stmt = $conn->prepare("UPDATE contents SET title=?, class_id=?, material_id=?, semester_id=?, group_id=? WHERE id=?");
                $stmt->execute([$title, $class_id, $material_id, $semester_id, $group_id, $content_id]);
                $msg = '<div class="alert alert-success">تم تعديل المحتوى بنجاح</div>';
            } else {
                $stmt = $conn->prepare("INSERT INTO contents (title, class_id, material_id, semester_id, group_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $class_id, $material_id, $semester_id, $group_id]);
                $msg = '<div class="alert alert-success">تمت إضافة المحتوى بنجاح</div>';
            }
        }
    }

    if (isset($_POST['delete']) && $content_id > 0) {
        $stmt = $conn->prepare("DELETE FROM contents WHERE id=?");
        $stmt->execute([$content_id]);
        $msg = '<div class="alert alert-success">تم حذف المحتوى بنجاح</div>';
    }
}

// تعبئة بيانات التعديل
$edit_id = intval($_GET['edit'] ?? 0);
$edit_row = null;
if ($edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM contents WHERE id=?");
    $stmt->execute([$edit_id]);
    $edit_row = $stmt->fetch(PDO::FETCH_ASSOC);
}

// جلب جميع المحتوى
$stmt = $conn->query("
    SELECT c.*,
           cl.name AS class_name,
           m.name AS material_name,
           s.name AS semester_name,
           g.name AS group_name
    FROM contents c
    LEFT JOIN classes cl ON c.class_id = cl.id
    LEFT JOIN materials m ON c.material_id = m.id
    LEFT JOIN semesters s ON c.semester_id = s.id
    LEFT JOIN groups g ON c.group_id = g.id
    ORDER BY c.id DESC
");
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المحتوى</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: #f7f7fa; }
        .container { margin-top: 30px; }
        .action-btns form { display: inline; }
    </style>
    <script>
    function loadMaterials(select) {
        var class_id = select.value;
        var matSelect = document.getElementById('material_id');
        matSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        fetch('contents.php?get_materials=1&class_id=' + class_id)
            .then(r=>r.json())
            .then(data=>{
                matSelect.innerHTML = '<option value="">اختر المادة</option>';
                data.forEach(function(mat){
                    var opt = document.createElement('option');
                    opt.value = mat.id;
                    opt.textContent = mat.name;
                    matSelect.appendChild(opt);
                });
                document.getElementById('group_id').innerHTML = '<option value="">اختر المجموعة</option>';
            });
    }

    function loadGroups() {
        var material_id = document.getElementById('material_id').value;
        var semester_id = document.querySelector('[name="semester_id"]').value;
        var grpSelect = document.getElementById('group_id');
        grpSelect.innerHTML = '<option value="">جاري التحميل...</option>';

        if (!material_id || !semester_id) {
            grpSelect.innerHTML = '<option value="">اختر المادة والفصل أولاً</option>';
            return;
        }

        fetch('contents.php?get_groups=1&material_id=' + material_id + '&semester_id=' + semester_id)
            .then(r => r.json())
            .then(data => {
                grpSelect.innerHTML = '<option value="">اختر المجموعة</option>';
                data.forEach(function(grp){
                    var opt = document.createElement('option');
                    opt.value = grp.id;
                    opt.textContent = grp.name;
                    grpSelect.appendChild(opt);
                });
            });
    }
    </script>
</head>
<body>
<div class="container">
    <h3 class="mb-4">إضافة محتوى / إدارة المحتوى</h3>
    <?php if ($msg) echo $msg; ?>
    <form method="post" class="row g-2 mb-4" autocomplete="off">
        <input type="hidden" name="content_id" value="<?= $edit_row['id'] ?? 0 ?>">
        <div class="col-md-2">
            <label class="form-label">الصف</label>
            <select class="form-select" name="class_id" required onchange="loadMaterials(this)">
                <option value="">اختر الصف</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= (isset($edit_row['class_id']) && $edit_row['class_id'] == $class['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">المادة</label>
            <select class="form-select" name="material_id" id="material_id" required onchange="loadGroups()">
                <option value="">اختر المادة</option>
                <?php
                if(isset($edit_row['class_id'])) {
                    $mats = $conn->prepare("SELECT id, name FROM materials WHERE class_id=?");
                    $mats->execute([$edit_row['class_id']]);
                    foreach($mats as $mat) {
                        $sel = (isset($edit_row['material_id']) && $edit_row['material_id'] == $mat['id']) ? 'selected' : '';
                        echo "<option value=\"{$mat['id']}\" $sel>{$mat['name']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">الفصل</label>
            <select class="form-select" name="semester_id" required onchange="loadGroups()">
                <option value="">اختر الفصل</option>
                <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['id'] ?>" <?= (isset($edit_row['semester_id']) && $edit_row['semester_id'] == $sem['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sem['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">المجموعة</label>
            <select class="form-select" name="group_id" id="group_id" required>
                <option value="">اختر المجموعة</option>
                <?php
                if(isset($edit_row['material_id']) && isset($edit_row['semester_id'])) {
                    $grps = $conn->prepare("SELECT id, name FROM groups WHERE material_id=? AND semester_id=?");
                    $grps->execute([$edit_row['material_id'], $edit_row['semester_id']]);
                    foreach($grps as $grp) {
                        $sel = (isset($edit_row['group_id']) && $edit_row['group_id'] == $grp['id']) ? 'selected' : '';
                        echo "<option value=\"{$grp['id']}\" $sel>{$grp['name']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">عنوان المحتوى</label>
            <input type="text" class="form-control" name="title" required value="<?= htmlspecialchars($edit_row['title'] ?? '') ?>">
        </div>
        <div class="col-md-12 mt-2 d-flex gap-1">
            <button type="submit" name="save" class="btn btn-success"><?= $edit_row ? 'تعديل' : 'إضافة' ?></button>
            <?php if ($edit_row): ?>
                <a href="contents.php" class="btn btn-secondary">إلغاء</a>
            <?php endif; ?>
        </div>
    </form>

    <h5>قائمة المحتوى</h5>
    <table class="table table-bordered table-striped">
        <tr>
            <th>#</th>
            <th>الصف</th>
            <th>المادة</th>
            <th>الفصل</th>
            <th>المجموعة</th>
            <th>العنوان</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach ($contents as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['class_name']) ?></td>
            <td><?= htmlspecialchars($row['material_name']) ?></td>
            <td><?= htmlspecialchars($row['semester_name']) ?></td>
            <td><?= htmlspecialchars($row['group_name']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td class="action-btns">
                <a href="contents.php?edit=<?= $row['id'] ?>" class="btn btn-primary btn-sm">تعديل</a>
                <form method="post" style="display:inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                    <input type="hidden" name="content_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm">حذف</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($contents)): ?>
        <tr>
            <td colspan="7" class="text-center">لا يوجد محتوى</td>
        </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>