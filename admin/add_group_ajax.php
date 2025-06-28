<?php
// 📄 admin/ajax_update_group.php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';

if (!$id || !$name) {
  echo json_encode(['success' => false, 'message' => 'يجب إدخال جميع الحقول']);
  exit;
}

$stmt = $conn->prepare("UPDATE groups SET title = ? WHERE id = ?");
$stmt->bind_param("si", $name, $id);
if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => '✅ تم تعديل اسم المجموعة']);
} else {
  echo json_encode(['success' => false, 'message' => '❌ فشل التعديل']);
}
?>
