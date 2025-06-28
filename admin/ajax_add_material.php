<?php
// 📄 admin/ajax_add_material.php
header('Content-Type: application/json; charset=utf-8');
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $class_id = intval($_POST['class_id'] ?? 0);
  $name = trim($_POST['name'] ?? '');

  if (!$class_id || empty($name)) {
    echo json_encode(['status' => 'error', 'message' => 'الرجاء ملء جميع الحقول']);
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO materials (class_id, subject) VALUES (?, ?)");
  $stmt->bind_param("is", $class_id, $name);
  $success = $stmt->execute();

  if ($success) {
    echo json_encode(['status' => 'success', 'message' => '✅ تم إضافة المادة بنجاح']);
  } else {
    echo json_encode(['status' => 'error', 'message' => '❌ فشل في إضافة المادة']);
  }
}