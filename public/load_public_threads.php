<?php
// 📄 public/load_public_threads.php
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// ✅ استعلام بدون تكرار: أخذ أول مرفق فقط مع كل موضوع
$sql = "
SELECT t.id, t.title, t.description,
       (SELECT a.file_path FROM attachments a WHERE a.thread_id = t.id ORDER BY a.id ASC LIMIT 1) AS file_path,
       (SELECT a.file_name FROM attachments a WHERE a.thread_id = t.id ORDER BY a.id ASC LIMIT 1) AS file_name
FROM threads t
ORDER BY t.id DESC
LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<div class='thread-card p-4 bg-white rounded shadow'>";
    echo "<h5 class='font-bold text-blue-700 mb-2'>📘 " . htmlspecialchars($row['title']) . "</h5>";
    
    $desc = mb_substr(strip_tags($row['description']), 0, 200) . '...';
    echo "<p class='text-sm text-gray-600 thread-body'>" . htmlspecialchars($desc) . " <a href='view.php?id={$row['id']}' class='text-primary'>اقرأ المزيد</a></p>";
    
    if (!empty($row['file_path'])) {
      $safePath = htmlspecialchars($row['file_path']);
      $safeName = htmlspecialchars($row['file_name']);
      echo "<a href='../{$safePath}' target='_blank' class='block mt-2 text-sm text-blue-600 hover:underline'>📎 تحميل: {$safeName}</a>";
    }
    
    echo "</div>";
  }
} else {
  echo "<p>لا توجد مواضيع حالياً.</p>";
}

// ✅ ترقيم الصفحات
$total = $conn->query("SELECT COUNT(*) as c FROM threads")->fetch_assoc()['c'];
$total_pages = ceil($total / $limit);

if ($total_pages > 1) {
  echo "<ul class='pagination justify-center mt-4'>";
  for ($i = 1; $i <= $total_pages; $i++) {
    $active = $i == $page ? 'active' : '';
    echo "<li class='page-item $active'><a class='page-link' href='#' onclick='loadLatest($i)'>$i</a></li>";
  }
  echo "</ul>";
}

$conn->close();
?>