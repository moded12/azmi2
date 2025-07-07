<?php
require_once "includes/db.php";

$action = $_REQUEST['action'] ?? '';

if($action == 'fetch') {
    $class_id = isset($_GET['class_id']) && $_GET['class_id'] != "" ? intval($_GET['class_id']) : 0;
    if ($class_id) {
        $stmt = $conn->prepare("SELECT m.*, c.name as class_name FROM materials m JOIN classes c ON m.class_id=c.id WHERE m.class_id=? ORDER BY m.id DESC");
        $stmt->execute([$class_id]);
    } else {
        $stmt = $conn->query("SELECT m.*, c.name as class_name FROM materials m JOIN classes c ON m.class_id=c.id ORDER BY m.id DESC");
    }

    echo '<table class="table table-striped table-bordered">';
    echo '<tr><th>#</th><th>اسم المادة</th><th>الصف</th><th>إجراءات</th></tr>';
    foreach($stmt as $row) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>".htmlspecialchars($row['name'])."</td>
            <td>".htmlspecialchars($row['class_name'])."</td>
            <td class='action-btns'>
                <button class='btn btn-sm btn-primary edit-btn' data-id='{$row['id']}'>تعديل</button>
                <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['id']}'>حذف</button>
            </td>
        </tr>";
    }
    echo '</table>';
    exit;
}

if($action == 'save') {
    $name = trim($_POST['material_name']);
    $class_id = intval($_POST['class_id']);
    $material_id = (isset($_POST['material_id']) && $_POST['material_id'] !== "" && intval($_POST['material_id']) > 0) ? intval($_POST['material_id']) : 0;

    if(!$name || !$class_id) {
        echo '<div class="alert alert-danger">جميع الحقول مطلوبة!</div>';
        exit;
    }

    if($material_id > 0) {
        $stmt = $conn->prepare("UPDATE materials SET name=?, class_id=? WHERE id=?");
        $stmt->execute([$name, $class_id, $material_id]);
        echo '<div class="alert alert-success">تم تعديل المادة بنجاح</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO materials (name, class_id) VALUES (?,?)");
        $stmt->execute([$name, $class_id]);
        echo '<div class="alert alert-success">تمت إضافة المادة بنجاح</div>';
    }
    exit;
}

if($action == 'get') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("SELECT * FROM materials WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($row);
    exit;
}

if($action == 'delete') {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM materials WHERE id=?");
    $stmt->execute([$id]);
    echo '<div class="alert alert-success">تم حذف المادة بنجاح</div>';
    exit;
}