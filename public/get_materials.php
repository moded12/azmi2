<?php
$conn = new mysqli('localhost', 'azmi3', 'Tvvcrtv1610@', 'azmi3');
$conn->set_charset('utf8mb4');
$class_id = intval($_GET['class_id'] ?? 0);

$list = [];
if ($class_id) {
    $q = $conn->query("SELECT id, name FROM materials WHERE class_id=$class_id ORDER BY id");
    while($row = $q->fetch_assoc()) $list[] = $row;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($list);