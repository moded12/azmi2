<?php
// 📄 api/classes.php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
  echo json_encode([]);
  exit;
}

$result = $conn->query("SELECT id, name FROM classes ORDER BY id ASC");
$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}
echo json_encode($data);