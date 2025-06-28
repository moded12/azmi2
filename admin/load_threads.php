<?php
// 📄 admin/load_threads.php
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset("utf8mb4");


if ($conn->connect_error) {
  die("فشل الاتصال بقاعدة البيانات");
}

$sql = "SELECT t.id, t.title, t.description, g.title AS group_name
        FROM threads t
        JOIN groups g ON t.group_id = g.id
        ORDER BY t.id DESC";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<div class='border rounded p-3 bg-white mb-3'>";
    echo "<h5 class='font-bold mb-1'>📌 {$row['title']}</h5>";
    echo "<p class='text-sm text-gray-600'>المجموعة: {$row['group_name']}</p>";
    echo "<p>{$row['description']}</p>";

    // المرفقات
    $thread_id = $row['id'];
    $a_sql = "SELECT file_name, file_path FROM attachments WHERE thread_id = $thread_id";
    $a_result = $conn->query($a_sql);
    if ($a_result->num_rows > 0) {
      echo "<div class='mt-2'><strong>📎 المرفقات:</strong><ul class='list-disc ps-5'>";
      while ($att = $a_result->fetch_assoc()) {
        $file_url = $att['file_path'];
        $file_name = $att['file_name'];
        echo "<li><a href='$file_url' target='_blank' class='text-blue-600 underline'>$file_name</a></li>";
      }
      echo "</ul></div>";
    }

    echo "</div>";
  }
} else {
  echo "<p>لا توجد مواضيع حالياً.</p>";
}
$conn->close();