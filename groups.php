<?php
require_once "includes/db.php";

// جلب الصفوف (classes)
$classes = $conn->query("SELECT id, name FROM classes")->fetchAll(PDO::FETCH_ASSOC);

// جلب الفصول
$semesters = $conn->query("SELECT id, name FROM semesters")->fetchAll(PDO::FETCH_ASSOC);

// لجلب المواد الخاصة بصف معين عبر AJAX
if(isset($_GET['get_materials']) && intval($_GET['class_id'])) {
    $class_id = intval($_GET['class_id']);
    $mats = $conn->prepare("SELECT id, name FROM materials WHERE class_id=?");
    $mats->execute([$class_id]);
    echo json_encode($mats->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// العمليات: إضافة/تعديل/حذف
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['group_name']);
    $desc = trim($_POST['group_desc'] ?? "");
    $class_id = intval($_POST['class_id'] ?? 0);
    $material_id = intval($_POST['material_id'] ?? 0);
    $semester_id = intval($_POST['semester_id'] ?? 0);
    $group_id = intval($_POST['group_id'] ?? 0);

    if (isset($_POST['save'])) {
        if ($name == '' || $class_id == 0 || $material_id == 0 || $semester_id == 0) {
            $msg = '<div class="alert alert-danger">جميع الحقول مطلوبة!</div>';
        } else {
            if ($group_id > 0) {
                $stmt = $conn->prepare("UPDATE `groups` SET name=?, description=?, class_id=?, material_id=?, semester_id=? WHERE id=?");
                $stmt->execute([$name, $desc, $class_id, $material_id, $semester_id, $group_id]);
                $msg = '<div class="alert alert-success">تم تعديل المجموعة بنجاح</div>';
            } else {
                $stmt = $conn->prepare("INSERT INTO `groups` (name, description, class_id, material_id, semester_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $desc, $class_id, $material_id, $semester_id]);
                $msg = '<div class="alert alert-success">تمت إضافة المجموعة بنجاح</div>';
            }
        }
    }

    if (isset($_POST['delete']) && $group_id > 0) {
        $stmt = $conn->prepare("DELETE FROM `groups` WHERE id=?");
        $stmt->execute([$group_id]);
        $msg = '<div class="alert alert-success">تم حذف المجموعة بنجاح</div>';
    }
}

// تعبئة بيانات التعديل
$edit_id = intval($_GET['edit'] ?? 0);
$edit_row = null;
if ($edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM `groups` WHERE id=?");
    $stmt->execute([$edit_id]);
    $edit_row = $stmt->fetch(PDO::FETCH_ASSOC);
}

// جلب كل المجموعات مع أسماء الصف والمادة والفصل
$stmt = $conn->query("
    SELECT g.*, c.name AS class_name, m.name AS material_name, s.name AS semester_name 
    FROM `groups` g 
    LEFT JOIN classes c ON g.class_id = c.id
    LEFT JOIN materials m ON g.material_id = m.id
    LEFT JOIN semesters s ON g.semester_id = s.id
    ORDER BY g.id DESC
");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة مجموعة</title>
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
        fetch('groups.php?get_materials=1&class_id=' + class_id)
            .then(r=>r.json())
            .then(data=>{
                matSelect.innerHTML = '<option value="">اختر المادة</option>';
                data.forEach(function(mat){
                    var opt = document.createElement('option');
                    opt.value = mat.id;
                    opt.textContent = mat.name;
                    matSelect.appendChild(opt);
                });
            });
    }
    </script>
</head>
<body>
<div class="container">
    <h3 class="mb-4">إضافة مجموعة / إدارة المجموعات</h3>
    <?php if ($msg) echo $msg; ?>
    <form method="post" class="row g-2 mb-4" autocomplete="off">
        <input type="hidden" name="group_id" value="<?= $edit_row['id'] ?? 0 ?>">
        <div class="col-md-3">
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
        <div class="col-md-3">
            <label class="form-label">المادة</label>
            <select class="form-select" name="material_id" id="material_id" required>
                <option value="">اختر المادة</option>
                <?php
                // عند التعديل، تحميل المواد المناسبة للصف المحدد
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
            <select class="form-select" name="semester_id" required>
                <option value="">اختر الفصل</option>
                <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['id'] ?>" <?= (isset($edit_row['semester_id']) && $edit_row['semester_id'] == $sem['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sem['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">اسم المجموعة</label>
            <input type="text" class="form-control" name="group_name" required value="<?= htmlspecialchars($edit_row['name'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">وصف المجموعة</label>
            <input type="text" class="form-control" name="group_desc" value="<?= htmlspecialchars($edit_row['description'] ?? '') ?>">
        </div>
        <div class="col-md-12 mt-2 d-flex gap-1">
            <button type="submit" name="save" class="btn btn-success"><?= $edit_row ? 'تعديل' : 'إضافة' ?></button>
            <?php if ($edit_row): ?>
                <a href="groups.php" class="btn btn-secondary">إلغاء</a>
            <?php endif; ?>
        </div>
    </form>

    <h5>قائمة المجموعات</h5>
    <table class="table table-bordered table-striped">
        <tr>
            <th>#</th>
            <th>الصف</th>
            <th>المادة</th>
            <th>الفصل</th>
            <th>اسم المجموعة</th>
            <th>الوصف</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach ($groups as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['class_name']) ?></td>
            <td><?= htmlspecialchars($row['material_name']) ?></td>
            <td><?= htmlspecialchars($row['semester_name']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td class="action-btns">
                <a href="groups.php?edit=<?= $row['id'] ?>" class="btn btn-primary btn-sm">تعديل</a>
                <form method="post" style="display:inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                    <input type="hidden" name="group_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm">حذف</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($groups)): ?>
        <tr>
            <td colspan="7" class="text-center">لا توجد مجموعات</td>
        </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>