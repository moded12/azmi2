<?php
// 📄 public/search_threads.php
header('Content-Type:text/html; charset=utf-8');
$conn = new mysqli('localhost','azmi2','Tvvcrtv1610@','azmi2');
$conn->set_charset('utf8mb4');

// جمع الفلاتر
$where = [];
$params = [];

foreach (['class_id','material_id','semester_id','group_id','type'] as $f) {
  if (!empty($_GET[$f])) {
    $where[] = "$f = ?";
    $params[] = $_GET[$f];
  }
}

// الاستعلام
$sql = "SELECT t.id,title, description, type,
        (SELECT file_name FROM attachments WHERE thread_id = t.id LIMIT 1) AS attachment,
        (SELECT file_path FROM attachments WHERE thread_id = t.id LIMIT 1) AS path
      FROM threads t"
     . (count($where) ? ' WHERE '.implode(' AND ', $where) : '')
     . " ORDER BY t.id DESC";

$stmt = $conn->prepare($sql);
if ($params) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

// عرض النتائج
if ($res->num_rows) {
  while ($t = $res->fetch_assoc()) {
    echo "<div class='bg-white p-3 mb-3 rounded shadow thread-card'>";
    echo "<h5 class='text-primary'>📘 {$t['title']}</h5>";
    echo "<p>".nl2br(htmlspecialchars($t['description']))."</p>";
    if ($t['attachment']) {
      $url = $t['path'];
      if (strpos($url, 'http') === 0) {
        echo "<a href='$url' target='_blank' class='btn btn-outline-success mt-2'>🔗 افتح الرابط</a>";
      } else {
        echo "<a href='../$url' target='_blank' class='btn btn-outline-info mt-2'>📎 تحميل: {$t['attachment']}</a>";
      }
    }
    echo "</div>";
  }
} else {
  echo "<div class='alert alert-warning'>🚫 لا توجد نتائج مطابقة للبحث.</div>";
}

$conn->close();
?>