<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "includes/db.php";

$action = $_REQUEST['action'] ?? '';

if($action == 'fetch') {
    $stmt = $conn->query("SELECT * FROM `groups` ORDER BY id DESC");
    echo '<table class="table table-striped table-bordered">';
    echo '<tr><th>#</th><th>اسم المجموعة</th><th>الوصف</th><th>إجراءات</th></tr>';
    foreach($stmt as $row) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>".htmlspecialchars($row['name'])."</td>
            <td>".htmlspecialchars($row['description'])."</td>
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
    $name = trim($_POST['group_name']);
    $desc = trim($_POST['group_desc'] ?? "");
    $group_id = (isset($_POST['group_id']) && $_POST['group_id'] !== "" && intval($_POST['group_id']) > 0) ? intval($_POST['group_id']) : 0;

    if(!$name) {
        echo '<div class="alert alert-danger">اسم المجموعة مطلوب!</div>';
        exit;
    }

    if($group_id > 0) {
        $stmt = $conn->prepare("UPDATE `groups` SET name=?, description=? WHERE id=?");
        $stmt->execute([$name, $desc, $group_id]);
        echo '<div class="alert alert-success">تم تعديل المجموعة بنجاح</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO `groups` (name, description) VALUES (?,?)");
        $stmt->execute([$name, $desc]);
        echo '<div class="alert alert-success">تمت إضافة المجموعة بنجاح</div>';
    }
    exit;
}

if($action == 'get') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("SELECT * FROM `groups` WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($row);
    exit;
}

if($action == 'delete') {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM `groups` WHERE id=?");
    $stmt->execute([$id]);
    echo '<div class="alert alert-success">تم حذف المجموعة بنجاح</div>';
    exit;
}