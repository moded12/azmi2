<?php
header('Content-Type: application/json; charset=utf-8');
$conn = new mysqli('localhost', 'azmi3', 'Tvvcrtv1610@', 'azmi3');
$conn->set_charset('utf8mb4');
$action = $_GET['action'] ?? '';

if($action == 'list_threads'){
    $threads = [];
    // فلاتر البحث
    $search   = $conn->real_escape_string($_GET['search'] ?? '');
    $material = $conn->real_escape_string($_GET['material'] ?? '');
    $class_id = $conn->real_escape_string($_GET['class_id'] ?? '');
    $semester = $conn->real_escape_string($_GET['semester'] ?? '');
    $group    = $conn->real_escape_string($_GET['group'] ?? '');

    $where = [];
    if ($search)   $where[] = "(t.title LIKE '%$search%' OR t.description LIKE '%$search%')";
    if ($material) $where[] = "m.name = '$material'";
    if ($class_id) $where[] = "m.class_id = '$class_id'";
    if ($semester) $where[] = "s.name = '$semester'";
    if ($group)    $where[] = "g.name = '$group'";
    $whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';

    $sql = "SELECT
      t.id            AS thread_id,
      t.title         AS thread_title,
      t.description   AS thread_description,
      t.thumbnail     AS thumbnail,          -- أضفنا هذا الحقل
      t.content_type,
      g.name          AS group_name,
      s.name          AS semester_name,
      m.name          AS material_name,
      m.class_id,
      tf.file_path
    FROM threads t
    JOIN `groups` g   ON t.group_id    = g.id
    JOIN semesters s  ON g.semester_id = s.id
    JOIN materials m  ON s.material_id = m.id
    LEFT JOIN thread_files tf ON tf.thread_id = t.id
    $whereSql
    ORDER BY t.id DESC
    LIMIT 30";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $threads[] = $row;
    }
    echo json_encode(['status'=>'success', 'data'=>$threads], JSON_UNESCAPED_UNICODE);
    exit;
}
?>