<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "includes/db.php";

// مكان حفظ المرفقات
$upload_dir = __DIR__ . "/uploads/";
$upload_url = "uploads/"; // للعرض في المتصفح

if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// حذف موضوع وكل مرفقاته
if (isset($_GET['delete_thread']) && is_numeric($_GET['delete_thread'])) {
    $thread_id = intval($_GET['delete_thread']);
    // حذف المرفقات من السيرفر
    $att = $conn->prepare("SELECT file_path FROM attachments WHERE thread_id=?");
    $att->execute([$thread_id]);
    foreach ($att->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $fp = __DIR__ . "/" . $row['file_path'];
        if (is_file($fp)) @unlink($fp);
    }
    // حذف من قاعدة البيانات
    $conn->prepare("DELETE FROM attachments WHERE thread_id=?")->execute([$thread_id]);
    $conn->prepare("DELETE FROM threads WHERE id=?")->execute([$thread_id]);
    header("Location: threads.php?msg=deleted");
    exit;
}

// حذف مرفق منفرد
if (isset($_GET['delete_attachment']) && is_numeric($_GET['delete_attachment'])) {
    $aid = intval($_GET['delete_attachment']);
    $att = $conn->prepare("SELECT file_path, thread_id FROM attachments WHERE id=?");
    $att->execute([$aid]);
    if ($attRow = $att->fetch(PDO::FETCH_ASSOC)) {
        $fp = __DIR__ . "/" . $attRow['file_path'];
        if (is_file($fp)) @unlink($fp);
        $conn->prepare("DELETE FROM attachments WHERE id=?")->execute([$aid]);
        header("Location: threads.php?edit={$attRow['thread_id']}&msg=del_att");
        exit;
    }
}

// جلب الصفوف والفصول
$classes = $conn->query("SELECT id, name FROM classes")->fetchAll(PDO::FETCH_ASSOC);
$semesters = $conn->query("SELECT id, name FROM semesters")->fetchAll(PDO::FETCH_ASSOC);

// جلب المواد عبر AJAX
if (isset($_GET['get_materials']) && intval($_GET['class_id'])) {
    $class_id = intval($_GET['class_id']);
    $mats = $conn->prepare("SELECT id, name FROM materials WHERE class_id=?");
    $mats->execute([$class_id]);
    echo json_encode($mats->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// جلب المجموعات عبر AJAX (مع class_id)
if (isset($_GET['get_groups']) && intval($_GET['material_id'])) {
    $material_id = intval($_GET['material_id']);
    $class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
    if ($class_id) {
        $grps = $conn->prepare("SELECT id, name FROM groups WHERE material_id=? AND class_id=?");
        $grps->execute([$material_id, $class_id]);
    } else {
        // fallback في حال لم يصل class_id
        $grps = $conn->prepare("SELECT id, name FROM groups WHERE material_id=?");
        $grps->execute([$material_id]);
    }
    echo json_encode($grps->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// فلترة المواضيع عبر AJAX
if (isset($_GET['ajax_list']) && $_GET['ajax_list'] == 1) {
    $where = [];
    $params = [];
    if (isset($_GET['class_id']) && intval($_GET['class_id'])) {
        $where[] = 't.class_id=?';
        $params[] = intval($_GET['class_id']);
    }
    if (isset($_GET['material_id']) && intval($_GET['material_id'])) {
        $where[] = 't.material_id=?';
        $params[] = intval($_GET['material_id']);
    }
    if (isset($_GET['semester_id']) && intval($_GET['semester_id'])) {
        $where[] = 't.semester_id=?';
        $params[] = intval($_GET['semester_id']);
    }
    if (isset($_GET['group_id']) && intval($_GET['group_id'])) {
        $where[] = 't.group_id=?';
        $params[] = intval($_GET['group_id']);
    }

    $sql = "SELECT t.*, c.name AS class_name, m.name AS mat_name FROM threads t
            LEFT JOIN classes c ON t.class_id=c.id
            LEFT JOIN materials m ON t.material_id=m.id";
    if ($where) $sql .= " WHERE " . implode(" AND ", $where);
    $sql .= " ORDER BY t.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $threads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($threads as $th) {
        echo "<tr>
            <td>".htmlspecialchars($th['title'])."</td>
            <td>".nl2br(htmlspecialchars($th['body']))."</td>
            <td>".htmlspecialchars($th['class_name'])."</td>
            <td>".htmlspecialchars($th['mat_name'])."</td>
            <td>".htmlspecialchars($th['created_at'])."</td>
            <td>
                <a href=\"view-thread.php?id={$th['id']}\" class=\"btn btn-sm btn-info\">عرض الموضوع</a>
                <a href=\"threads.php?edit={$th['id']}\" class=\"btn btn-sm btn-primary\">تعديل</a>
                <a href=\"threads.php?delete_thread={$th['id']}\" class=\"btn btn-sm btn-danger\" onclick=\"return confirm('حذف الموضوع وجميع مرفقاته؟')\">حذف</a>
            </td>
        </tr>";
    }
    exit;
}

$msg = '';
// إضافة أو تعديل موضوع
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? "");
    $body = trim($_POST['body'] ?? "");
    $class_id = intval($_POST['class_id'] ?? 0);
    $material_id = intval($_POST['material_id'] ?? 0);
    $semester_id = intval($_POST['semester_id'] ?? 0);
    $group_id = intval($_POST['group_id'] ?? 0);

    if ($title == '' || $body == '' || $class_id == 0 || $material_id == 0 || $semester_id == 0 || $group_id == 0) {
        $msg = '<div class="alert alert-danger">جميع الحقول مطلوبة!</div>';
    } else {
        // تعديل
        if (isset($_POST['edit_id']) && is_numeric($_POST['edit_id'])) {
            $edit_id = intval($_POST['edit_id']);
            $stmt = $conn->prepare("UPDATE threads SET title=?, body=?, class_id=?, material_id=?, semester_id=?, group_id=? WHERE id=?");
            $stmt->execute([$title, $body, $class_id, $material_id, $semester_id, $group_id, $edit_id]);
            $thread_id = $edit_id;
            $msg = '<div class="alert alert-success">تم تعديل الموضوع بنجاح</div>';
        } else { // إضافة
            $stmt = $conn->prepare("INSERT INTO threads (title, body, class_id, material_id, semester_id, group_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $body, $class_id, $material_id, $semester_id, $group_id]);
            $thread_id = $conn->lastInsertId();
            $msg = '<div class="alert alert-success">تمت إضافة الموضوع مع المرفقات بنجاح</div>';
        }

        // رفع مرفقات
        if (isset($_FILES['attachments']) && $_FILES['attachments']['error'][0] != 4 && $thread_id) {
            $allowed = ['pdf','jpg','jpeg','png','gif','doc','docx','xls','xlsx','zip','rar'];
            foreach ($_FILES['attachments']['tmp_name'] as $i => $tmp_name) {
                if ($_FILES['attachments']['error'][$i] === 0) {
                    $orig = basename($_FILES['attachments']['name'][$i]);
                    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed)) continue;
                    $new_name = uniqid('att_') . '.' . $ext;
                    $target = $upload_dir . $new_name;
                    if (move_uploaded_file($tmp_name, $target)) {
                        $conn->prepare("INSERT INTO attachments (thread_id, file_name, file_path) VALUES (?, ?, ?)")
                            ->execute([$thread_id, $orig, $upload_url . $new_name]);
                    }
                }
            }
        }
        if (isset($edit_id)) header("Location: threads.php?edit=$edit_id&msg=edited");
        else header("Location: threads.php?msg=added");
        exit;
    }
}

// جلب بيانات موضوع للتعديل
$editing = false;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_row = $conn->prepare("SELECT * FROM threads WHERE id=?");
    $edit_row->execute([$edit_id]);
    if ($row = $edit_row->fetch(PDO::FETCH_ASSOC)) {
        $editing = true;
        $edit_title = htmlspecialchars($row['title']);
        $edit_body = htmlspecialchars($row['body']);
        $edit_class = $row['class_id'];
        $edit_material = $row['material_id'];
        $edit_semester = $row['semester_id'];
        $edit_group = $row['group_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المواضيع</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: #f7f7fa; }
        .container { margin-top: 30px; }
        textarea.form-control { min-height: 60px; }
        .att-link { margin-left: 10px; }
    </style>
    <script>
    // تحميل المواد عند تغيير الصف (فلتر للنموذج العادي)
    function loadMaterials(select) {
        var class_id = select.value;
        var matSelect = document.getElementById('material_id');
        matSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        fetch('threads.php?get_materials=1&class_id=' + class_id)
            .then(r=>r.json())
            .then(data=>{
                matSelect.innerHTML = '<option value="">اختر المادة</option>';
                data.forEach(function(mat){
                    var opt = document.createElement('option');
                    opt.value = mat.id;
                    opt.textContent = mat.name;
                    matSelect.appendChild(opt);
                });
                // إعادة تعيين المجموعات عند تغيير الصف
                var grpSelect = document.getElementById('group_id');
                if (grpSelect) grpSelect.innerHTML = '<option value="">اختر المجموعة</option>';
            });
    }
    // تحميل المجموعات عند تغيير المادة (فلتر للنموذج العادي)
    function loadGroups(select) {
        var material_id = select.value;
        var class_id = document.querySelector('select[name="class_id"]').value;
        var grpSelect = document.getElementById('group_id');
        grpSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        fetch('threads.php?get_groups=1&material_id=' + material_id + '&class_id=' + class_id)
            .then(r=>r.json())
            .then(data=>{
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
    <h3 class="mb-4"><?= $editing ? 'تعديل موضوع' : 'إضافة موضوع جديد' ?></h3>
    <?php
        if (isset($_GET['msg']) && $_GET['msg']=='deleted') echo '<div class="alert alert-success">تم حذف الموضوع وكل مرفقاته بنجاح</div>';
        if (isset($_GET['msg']) && $_GET['msg']=='added') echo '<div class="alert alert-success">تمت إضافة الموضوع مع المرفقات بنجاح</div>';
        if (isset($_GET['msg']) && $_GET['msg']=='edited') echo '<div class="alert alert-success">تم تعديل الموضوع بنجاح</div>';
        if (isset($_GET['msg']) && $_GET['msg']=='del_att') echo '<div class="alert alert-success">تم حذف المرفق بنجاح</div>';
        if ($msg) echo $msg;
    ?>
    <form method="post" enctype="multipart/form-data" class="row g-2 mb-4" autocomplete="off">
        <?php if($editing): ?><input type="hidden" name="edit_id" value="<?= $edit_id ?>"><?php endif;?>
        <div class="col-md-2">
            <label class="form-label">الصف</label>
            <select class="form-select" name="class_id" required onchange="loadMaterials(this)">
                <option value="">اختر الصف</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= $editing && $edit_class==$class['id']?'selected':'' ?>><?= htmlspecialchars($class['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">المادة</label>
            <select class="form-select" name="material_id" id="material_id" required onchange="loadGroups(this)">
                <option value="">اختر المادة</option>
                <?php if($editing):
                    $mats = $conn->prepare("SELECT id, name FROM materials WHERE class_id=?");
                    $mats->execute([$edit_class]);
                    foreach($mats->fetchAll(PDO::FETCH_ASSOC) as $mat):?>
                        <option value="<?= $mat['id'] ?>" <?= $edit_material==$mat['id']?'selected':'' ?>><?= htmlspecialchars($mat['name']) ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">الفصل</label>
            <select class="form-select" name="semester_id" required>
                <option value="">اختر الفصل</option>
                <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['id'] ?>" <?= $editing && $edit_semester==$sem['id']?'selected':'' ?>><?= htmlspecialchars($sem['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">المجموعة</label>
            <select class="form-select" name="group_id" id="group_id" required>
                <option value="">اختر المجموعة</option>
                <?php if($editing):
                    $grps = $conn->prepare("SELECT id, name FROM groups WHERE material_id=? AND class_id=?");
                    $grps->execute([$edit_material, $edit_class]);
                    foreach($grps->fetchAll(PDO::FETCH_ASSOC) as $grp):?>
                        <option value="<?= $grp['id'] ?>" <?= $edit_group==$grp['id']?'selected':'' ?>><?= htmlspecialchars($grp['name']) ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">عنوان الموضوع</label>
            <input type="text" class="form-control" name="title" required value="<?= $editing?$edit_title:'' ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">نص الموضوع</label>
            <textarea class="form-control" name="body" required><?= $editing?$edit_body:'' ?></textarea>
        </div>
        <div class="col-md-12">
            <label class="form-label">المرفقات (اختياري)</label>
            <input type="file" name="attachments[]" multiple class="form-control">
            <small>يمكن رفع صور أو ملفات PDF أو مستندات أو مضغوطة.</small>
            <?php if($editing):
                $atts = $conn->prepare("SELECT * FROM attachments WHERE thread_id=?");
                $atts->execute([$edit_id]);
                foreach($atts->fetchAll(PDO::FETCH_ASSOC) as $att): ?>
                <div>
                    <a class="att-link" href="<?= htmlspecialchars($att['file_path']) ?>" target="_blank"><?= htmlspecialchars($att['file_name']) ?></a>
                    <a href="threads.php?delete_attachment=<?= $att['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف هذا المرفق؟')">حذف</a>
                </div>
            <?php endforeach; endif;?>
        </div>
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-success"><?= $editing?'تعديل':'إضافة' ?></button>
            <?php if($editing): ?>
                <a href="threads.php" class="btn btn-secondary">إلغاء</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- نموذج الفلاتر أعلى الجدول -->
    <form id="filterForm" class="row g-2 mb-3" onsubmit="return false;">
        <div class="col-md-2">
            <select class="form-select" id="filter_class_id" name="class_id">
                <option value="">كل الصفوف</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" id="filter_material_id" name="material_id">
                <option value="">كل المواد</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" id="filter_semester_id" name="semester_id">
                <option value="">كل الفصول</option>
                <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['id'] ?>"><?= htmlspecialchars($sem['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" id="filter_group_id" name="group_id">
                <option value="">كل المجموعات</option>
            </select>
        </div>
    </form>

    <!-- جدول عرض المواضيع -->
    <table class="table table-bordered" id="filtered_threads">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>النص</th>
                <th>الصف</th>
                <th>المادة</th>
                <th>تاريخ الإضافة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <!-- سيتم ملؤها بالمواضيع بعد الفلترة -->
        </tbody>
    </table>
</div>

<script>
// تحميل المواد عند تغيير الصف (فلتر للجدول)
document.getElementById('filter_class_id').addEventListener('change', function() {
    var class_id = this.value;
    var matSelect = document.getElementById('filter_material_id');
    matSelect.innerHTML = '<option value="">جاري التحميل...</option>';
    fetch('threads.php?get_materials=1&class_id=' + class_id)
        .then(r=>r.json())
        .then(data=>{
            matSelect.innerHTML = '<option value="">كل المواد</option>';
            data.forEach(function(mat){
                var opt = document.createElement('option');
                opt.value = mat.id;
                opt.textContent = mat.name;
                matSelect.appendChild(opt);
            });
            document.getElementById('filter_group_id').innerHTML = '<option value="">كل المجموعات</option>';
            fetchFilteredThreads(); // فلترة مباشرة عند تغيير الصف
        });
});

// تحميل المجموعات عند تغيير المادة (فلتر للجدول)
document.getElementById('filter_material_id').addEventListener('change', function() {
    var material_id = this.value;
    var class_id = document.getElementById('filter_class_id').value;
    var grpSelect = document.getElementById('filter_group_id');
    grpSelect.innerHTML = '<option value="">جاري التحميل...</option>';
    fetch('threads.php?get_groups=1&material_id=' + material_id + '&class_id=' + class_id)
        .then(r=>r.json())
        .then(data=>{
            grpSelect.innerHTML = '<option value="">كل المجموعات</option>';
            data.forEach(function(grp){
                var opt = document.createElement('option');
                opt.value = grp.id;
                opt.textContent = grp.name;
                grpSelect.appendChild(opt);
            });
            fetchFilteredThreads(); // فلترة مباشرة عند تغيير المادة
        });
});

// فلترة مباشرة عند تغيير الفصل أو المجموعة
document.getElementById('filter_semester_id').addEventListener('change', fetchFilteredThreads);
document.getElementById('filter_group_id').addEventListener('change', fetchFilteredThreads);

// جلب المواضيع المفلترة
function fetchFilteredThreads() {
    var class_id = document.getElementById('filter_class_id').value;
    var material_id = document.getElementById('filter_material_id').value;
    var semester_id = document.getElementById('filter_semester_id').value;
    var group_id = document.getElementById('filter_group_id').value;
    let params = [];
    if (class_id) params.push('class_id='+class_id);
    if (material_id) params.push('material_id='+material_id);
    if (semester_id) params.push('semester_id='+semester_id);
    if (group_id) params.push('group_id='+group_id);

    fetch('threads.php?ajax_list=1&'+params.join('&'))
        .then(r=>r.text())
        .then(html=>{
            document.querySelector('#filtered_threads tbody').innerHTML = html;
        });
}

// أول تحميل: جلب الكل
document.addEventListener('DOMContentLoaded', function() {
    fetchFilteredThreads();
});
</script>
</body>
</html>