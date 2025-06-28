<?php
// 📄 api/materials.php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
  echo json_encode([]);
  exit;
}

// Optional: filter by class_id if passed
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$sql = "SELECT id, subject AS name FROM materials" . ($class_id ? " WHERE class_id = $class_id" : "") . " ORDER BY id ASC";

$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}
echo json_encode($data);