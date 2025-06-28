<?php
// 📄 api/groups.php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
  echo json_encode([]);
  exit;
}

// Optional filters
$material_id = isset($_GET['material_id']) ? intval($_GET['material_id']) : 0;
$semester_id = isset($_GET['semester_id']) ? intval($_GET['semester_id']) : 0;
$where = [];
if ($material_id) $where[] = "material_id = $material_id";
if ($semester_id) $where[] = "semester_id = $semester_id";
$where_clause = count($where) ? "WHERE " . implode(' AND ', $where) : "";

$sql = "SELECT id, title AS name FROM groups $where_clause ORDER BY id ASC";
$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}
echo json_encode($data);