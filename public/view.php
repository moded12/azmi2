<?php
// 📄 public/view.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('localhost', 'azmi2', 'Tvvcrtv1610@', 'azmi2');
$conn->set_charset('utf8mb4');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$thread = null;
$attachments = [];

if ($id > 0) {
  $result = $conn->query("SELECT * FROM threads WHERE id = $id");
  if ($result && $result->num_rows > 0) {
    $thread = $result->fetch_assoc();

    $att = $conn->query("SELECT * FROM attachments WHERE thread_id = $id");
    if ($att && $att->num_rows > 0) {
      while ($a = $att->fetch_assoc()) {
        $filePath = $a['file_path'];

        // تصحيح المسار حسب نوع الرابط
        if (preg_match('/^https?:\/\//', $filePath)) {
          $correctPath = $filePath;
        } else {
          $cleanPath = ltrim(str_replace('public/', '', $filePath), '/');
          $correctPath = "https://www.shneler.com/azmi2/public/$cleanPath";
        }

        $a['correct_path'] = $correctPath;
        $a['file_ext'] = strtolower(pathinfo($correctPath, PATHINFO_EXTENSION));
        $attachments[] = $a;
      }
    }
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($thread['title'] ?? 'موضوع غير موجود') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet"/>
  <style>
    body { background-color: #111; color: white; font-family: 'Cairo', sans-serif; padding: 20px; }
    .viewer { margin: 20px auto; max-width: 80%; }
    iframe, img, video { max-width: 100%; border-radius: 8px; }
  </style>
</head>
<body class="text-center">

<?php if ($thread): ?>
  <h2><?= htmlspecialchars($thread['title']) ?></h2>
  <p><?= nl2br(htmlspecialchars($thread['description'])) ?></p>

  <?php if (!empty($attachments)): ?>
    <?php foreach ($attachments as $att): ?>
      <div class="viewer mt-4">
        <?php
          $ext = $att['file_ext'];
          $url = $att['correct_path'];

          if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            echo "<img src='$url' alt='مرفق' />";
          } elseif ($ext === 'pdf') {
            echo "<iframe src='https://docs.google.com/gview?url=$url&embedded=true' style='width:100%;height:600px;' frameborder='0'></iframe>";
          } elseif (in_array($ext, ['mp4', 'webm', 'ogg'])) {
            echo "<video controls><source src='$url' type='video/$ext'>المتصفح لا يدعم تشغيل الفيديو</video>";
          } elseif (preg_match('/^https?:\/\//', $url)) {
            echo "<a href='$url' target='_blank' class='btn btn-primary'>فتح الرابط الخارجي 🔗</a>";
          } else {
            echo "<a href='$url' target='_blank' class='btn btn-primary'>تحميل المرفق 📎</a>";
          }
        ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-danger mt-4">🚫 لا توجد ملفات مرفقة</p>
  <?php endif; ?>

<?php else: ?>
  <h3 class="text-warning">❌ الموضوع غير موجود</h3>
<?php endif; ?>

<a href="index.html" class="btn btn-light mt-5">العودة للرئيسية</a>

</body>
</html>