<?php
// 📄 view.php

$thread_id = intval($_GET['thread_id'] ?? 0);
$conn = new mysqli('localhost','azmi3','Tvvcrtv1610@','azmi3');
$conn->set_charset('utf8mb4');

$sql = "SELECT
    t.id             AS thread_id,
    t.title          AS thread_title,
    t.description    AS thread_description,
    t.thumbnail      AS thread_thumbnail,
    g.name           AS group_name,
    s.name           AS semester_name,
    m.name           AS material_name,
    m.class_id,
    tf.file_path
  FROM threads t
  JOIN `groups`    g ON t.group_id    = g.id
  JOIN semesters   s ON g.semester_id = s.id
  JOIN materials   m ON s.material_id = m.id
  LEFT JOIN thread_files tf ON tf.thread_id = t.id
  WHERE t.id = $thread_id";

$result = $conn->query($sql);
$thread = null;
$files  = [];
while($row = $result->fetch_assoc()) {
    if (!$thread) $thread = $row;
    if ($row['file_path']) $files[] = $row['file_path'];
}
if (!$thread) {
    die('<div class="text-center text-danger my-5">الموضوع غير موجود</div>');
}

// أيقونات عشوائية
$edu_icons = ["bi-mortarboard","bi-book","bi-globe","bi-lightbulb","bi-award"];
$avatar_icon = $edu_icons[array_rand($edu_icons)];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($thread['thread_title']) ?> - عرض الموضوع</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #eef6ff; min-height:100vh; }
    .card { border-radius:18px; box-shadow:0 4px 20px rgba(25,118,210,.2); }
    .avatar-edu {
      width:82px; height:82px; margin:0 auto -34px;
      border-radius:50%; background:linear-gradient(135deg,#b3e5fc 40%,#81d4fa 100%);
      display:flex; align-items:center; justify-content:center;
      border:4px solid #fff; box-shadow:0 2px 14px rgba(25,118,210,.07);
      font-size:2.5rem; color:#1976d2;
    }
    .main-header {
      background:linear-gradient(90deg,#2196f3 0%,#00bcd4 100%);
      color:#fff; text-align:center; padding:.75rem; border-radius:0 0 26px 26px;
      box-shadow:0 2px 12px rgba(25,118,210,.13);
      margin-bottom:1.5rem;
    }
    .file-thumb, .carousel-item img {
      max-width:100%; max-height:340px;
      border-radius:14px; background:#f5f5f9; margin:0 auto 1rem;
    }
    .pdf-iframe { width:100%; height:500px; border:1px solid #e0e7ef; border-radius:12px; }
    .back-btn { margin-bottom:1.5rem; }
    .thread-meta span { margin-inline-start:.5rem; }
    .footer { text-align:center; margin-top:2.5rem; color:#666; }
    @media (max-width:600px) {
      .avatar-edu { width:56px; height:56px; font-size:1.5rem; }
      .pdf-iframe { height:300px; }
    }
  </style>
</head>
<body>
  <div class="main-header">
    <h2>المنصة التعليمية <i class="bi bi-bookmark-fill"></i></h2>
  </div>

  <div class="container">
    <a href="index.php" class="btn btn-outline-primary back-btn">
      <i class="bi bi-arrow-right"></i> رجوع للمواضيع
    </a>
    <div class="card p-4 position-relative animate__fadeIn">
      <div class="avatar-edu"><i class="bi <?= $avatar_icon ?>"></i></div>
      <h2 class="text-center mt-4"><?= htmlspecialchars($thread['thread_title']) ?></h2>

      <!-- الصورة الكبيرة عند عرض الموضوع -->
      <?php if (!empty($thread['thread_thumbnail'])): ?>
        <div class="text-center mb-4">
          <img
            src="<?= htmlspecialchars($thread['thread_thumbnail']) ?>"
            alt="صورة الموضوع"
            style="max-width:100%; width:300px; height:auto; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1);"
          >
        </div>
      <?php endif; ?>

      <p class="text-muted text-center mb-4">
        <?= nl2br(htmlspecialchars($thread['thread_description'])) ?>
      </p>

      <div class="thread-meta text-center p-2 mb-4 bg-light rounded">
        <span class="badge bg-info text-dark"><?= htmlspecialchars($thread['material_name']) ?></span>
        <span class="badge bg-secondary"><?= htmlspecialchars($thread['semester_name']) ?></span>
        <span class="badge bg-primary"><?= htmlspecialchars($thread['group_name']) ?></span>
        <span class="badge bg-dark">الصف <?= htmlspecialchars($thread['class_id']) ?></span>
      </div>

      <?php if ($files): ?>
        <?php
          $img_exts = ['jpg','jpeg','png','gif','webp','bmp'];
          $pdf_exts = ['pdf'];
          $vid_exts = ['mp4','webm','mov','avi'];
          $idx = 1;
        ?>
        <?php foreach($files as $file):
          $is_ext = preg_match('#^https?://#',$file);
          $ext   = strtolower(pathinfo($file, PATHINFO_EXTENSION));
          $url   = $is_ext ? $file : '/azmi3/admin/'.$file;
        ?>
          <div class="mb-4">
            <?php if (in_array($ext,$img_exts)): ?>
              <h5><i class="bi bi-image"></i> صورة مرفقة <?= $idx++ ?></h5>
              <img src="<?= htmlspecialchars($url) ?>" class="file-thumb" alt="صورة مرفقة">
            <?php elseif(in_array($ext,$pdf_exts)): ?>
              <h5><i class="bi bi-file-earmark-pdf"></i> PDF</h5>
              <iframe src="<?= htmlspecialchars($url) ?>" class="pdf-iframe"></iframe>
              <a href="<?= htmlspecialchars($url) ?>" class="btn btn-outline-primary mt-2" target="_blank">
                <i class="bi bi-download"></i> تحميل
              </a>
            <?php elseif(in_array($ext,$vid_exts)): ?>
              <h5><i class="bi bi-camera-video"></i> فيديو</h5>
              <video src="<?= htmlspecialchars($url) ?>" controls style="width:100%;max-height:340px;border-radius:12px;"></video>
            <?php else: ?>
              <h5><i class="bi bi-link-45deg"></i> رابط</h5>
              <a href="<?= htmlspecialchars($url) ?>" class="btn btn-secondary" target="_blank"><?= htmlspecialchars($url) ?></a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-warning">لا يوجد مرفقات لهذا الموضوع.</div>
      <?php endif; ?>
    </div>

    <div class="footer">جميع الحقوق محفوظة © 2025 المنصة التعليمية</div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>