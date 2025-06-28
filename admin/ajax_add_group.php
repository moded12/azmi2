<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 📄 admin/ajax_add_group.php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material_id = intval($_POST['material_id'] ?? 0);
    $semester_id = intval($_POST['semester_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($material_id && $name !== '') {
        $stmt = $conn->prepare("INSERT INTO groups (material_id, semester_id, title) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $material_id, $semester_id, $name);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => '✅ تم إضافة المجموعة بنجاح']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ فشل في تنفيذ الاستعلام']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => '❌ يرجى تعبئة جميع الحقول']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '❌ الطلب غير صالح']);
}
?>