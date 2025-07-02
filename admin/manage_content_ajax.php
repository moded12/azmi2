<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli('localhost', 'azmi3', 'Tvvcrtv1610@', 'azmi3');
$conn->set_charset('utf8mb4');
$action = $_REQUEST['action'] ?? '';

/* =========== 1. القائمة الجانبية Accordion =========== */
if($action=='sidebar_list'){
  $classes = [];
  for($i=1;$i<=12;$i++) $classes[$i] = "الصف $i";
  $materials = $conn->query("SELECT id, name, class_id FROM materials ORDER BY class_id, id DESC");
  foreach($materials as $m){
    $material_id = intval($m['id']);
    $mid = "material{$material_id}";
    echo '<div class="accordion-item mb-2">';
    echo '<h2 class="accordion-header" id="heading-'.$mid.'">
      <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button"
              data-bs-toggle="collapse" data-bs-target="#collapse-'.$mid.'" aria-expanded="false" aria-controls="collapse-'.$mid.'">
        <span>'.htmlspecialchars($m['name']??"").' <span class="text-secondary fs-6">('.$classes[$m['class_id']??1].')</span></span>
      </button>
      <span class="ms-2 d-flex flex-column gap-1 align-items-end">
        <button class="btn btn-sm btn-outline-primary edit-item d-flex align-items-center gap-1" data-type="material" data-id="'.$material_id.'">
          <i class="bi bi-pencil-square"></i> تعديل
        </button>
        <button class="btn btn-sm btn-outline-danger delete-item d-flex align-items-center gap-1" data-type="material" data-id="'.$material_id.'">
          <i class="bi bi-trash"></i> حذف
        </button>
        <button class="btn btn-sm btn-outline-secondary view-item d-flex align-items-center gap-1" data-type="material" data-id="'.$material_id.'">
          <i class="bi bi-eye"></i> مشاهدة
        </button>
      </span>
    </h2>
    <div id="collapse-'.$mid.'" class="accordion-collapse collapse" aria-labelledby="heading-'.$mid.'" data-bs-parent="#sidebarList">
      <div class="accordion-body p-2">';
        $semesters = $conn->query("SELECT id, name FROM semesters WHERE material_id=$material_id");
        foreach($semesters as $s){
          $semester_id = intval($s['id']);
          $sid = "semester{$semester_id}_{$mid}";
          echo '<div class="accordion mb-1" id="semesters-'.$mid.'">
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-'.$sid.'">
                <button class="accordion-button collapsed py-1" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapse-'.$sid.'" aria-expanded="false" aria-controls="collapse-'.$sid.'">'
                        .htmlspecialchars($s['name']??"").
                '</button>
              </h2>
              <div id="collapse-'.$sid.'" class="accordion-collapse collapse" aria-labelledby="heading-'.$sid.'" data-bs-parent="#semesters-'.$mid.'">
                <div class="accordion-body p-2">';
                  $groups = $conn->query("SELECT id, name FROM groups WHERE semester_id=$semester_id");
                  foreach($groups as $g){
                    $group_id = intval($g['id']);
                    $gid = "group{$group_id}_{$sid}";
                    echo '<div class="accordion mb-1" id="groups-'.$sid.'">
                      <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-'.$gid.'">
                          <button class="accordion-button collapsed py-1" type="button"
                                  data-bs-toggle="collapse" data-bs-target="#collapse-'.$gid.'" aria-expanded="false" aria-controls="collapse-'.$gid.'">'
                                  .htmlspecialchars($g['name']??"").
                          '</button>
                          <span class="ms-1 d-flex flex-column gap-1 align-items-end">
                            <button class="btn btn-sm btn-outline-primary edit-item d-flex align-items-center gap-1" data-type="group" data-id="'.$group_id.'">
                              <i class="bi bi-pencil-square"></i> تعديل
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-item d-flex align-items-center gap-1" data-type="group" data-id="'.$group_id.'">
                              <i class="bi bi-trash"></i> حذف
                            </button>
                            <button class="btn btn-sm btn-outline-secondary view-item d-flex align-items-center gap-1" data-type="group" data-id="'.$group_id.'">
                              <i class="bi bi-eye"></i> مشاهدة
                            </button>
                          </span>
                        </h2>
                        <div id="collapse-'.$gid.'" class="accordion-collapse collapse" aria-labelledby="heading-'.$gid.'" data-bs-parent="#groups-'.$sid.'">
                          <div class="accordion-body p-2">';
                            $threads = $conn->query("SELECT id, title FROM threads WHERE group_id=$group_id ORDER BY id DESC LIMIT 10");
                            foreach($threads as $t){
                              echo '<div class="d-flex align-items-center justify-content-between thread-title mb-1">
                                <span>'.htmlspecialchars($t['title']??"").'</span>
                                <span class="d-flex flex-column gap-1 align-items-end">
                                  <button class="btn btn-sm btn-outline-primary edit-item d-flex align-items-center gap-1" data-type="thread" data-id="'.intval($t['id']).'">
                                    <i class="bi bi-pencil-square"></i> تعديل
                                  </button>
                                  <button class="btn btn-sm btn-outline-danger delete-item d-flex align-items-center gap-1" data-type="thread" data-id="'.intval($t['id']).'">
                                    <i class="bi bi-trash"></i> حذف
                                  </button>
                                  <button class="btn btn-sm btn-outline-secondary view-item d-flex align-items-center gap-1" data-type="thread" data-id="'.intval($t['id']).'">
                                    <i class="bi bi-eye"></i> مشاهدة
                                  </button>
                                </span>
                              </div>';
                            }
                            $cnt = $conn->query("SELECT COUNT(*) c FROM threads WHERE group_id=$group_id")->fetch_assoc();
                            if($cnt['c'] > 10){
                              echo '<button class="btn btn-link p-0" onclick="loadMoreThreads('.$group_id.',this)">عرض المزيد...</button>';
                            }
                          echo '</div>
                        </div>
                      </div>
                    </div>';
                  }
                echo '</div>
              </div>
            </div>
          </div>';
        }
      echo '</div>
    </div>
  </div>';
  }
  exit;
}

/* =========== 2. جلب خيارات البحث الديناميكي =========== */
if($action=='list_materials'){
  $class_id = intval($_GET['class_id'] ?? 0);
  $rows = $conn->query("SELECT id, name FROM materials WHERE class_id=$class_id ORDER BY name ASC");
  foreach($rows as $row) echo "<option value='{$row['id']}'>".htmlspecialchars($row['name'])."</option>";
  exit;
}
if($action=='list_semesters'){
  $material_id = intval($_GET['material_id'] ?? 0);
  $rows = $conn->query("SELECT id, name FROM semesters WHERE material_id=$material_id ORDER BY id ASC");
  foreach($rows as $row) echo "<option value='{$row['id']}'>".htmlspecialchars($row['name'])."</option>";
  exit;
}
if($action=='list_groups'){
  $semester_id = intval($_GET['semester_id'] ?? 0);
  $rows = $conn->query("SELECT id, name FROM groups WHERE semester_id=$semester_id ORDER BY id ASC");
  foreach($rows as $row) echo "<option value='{$row['id']}'>".htmlspecialchars($row['name'])."</option>";
  exit;
}
if($action=='list_threads'){
  $group_id = intval($_GET['group_id'] ?? 0);
  $rows = $conn->query("SELECT id, title FROM threads WHERE group_id=$group_id ORDER BY id DESC");
  foreach($rows as $row) echo "<option value='{$row['id']}'>".htmlspecialchars($row['title'])."</option>";
  exit;
}

/* =========== 3. نتائج البحث (جدول + إجراءات) =========== */
if($action=='search_results'){
  $where = [];
  if(!empty($_GET['class_id'])) $where[] = "materials.class_id=".(int)$_GET['class_id'];
  if(!empty($_GET['material_id'])) $where[] = "materials.id=".(int)$_GET['material_id'];
  if(!empty($_GET['semester_id'])) $where[] = "semesters.id=".(int)$_GET['semester_id'];
  if(!empty($_GET['group_id'])) $where[] = "groups.id=".(int)$_GET['group_id'];
  if(!empty($_GET['thread_id'])) $where[] = "threads.id=".(int)$_GET['thread_id'];

  $sql = "SELECT threads.*, groups.name as group_name, semesters.name as semester_name, materials.name as material_name, materials.class_id 
          FROM threads
          JOIN groups ON threads.group_id=groups.id
          JOIN semesters ON groups.semester_id=semesters.id
          JOIN materials ON semesters.material_id=materials.id";
  if($where) $sql .= " WHERE ".implode(" AND ", $where);
  $sql .= " ORDER BY threads.id DESC LIMIT 50";

  $res = $conn->query($sql);
  if($res->num_rows==0){
    echo "<div class='alert alert-warning'>لا يوجد نتائج.</div>";
  } else {
    echo "<div class='table-responsive'><table class='table table-bordered table-sm text-center align-middle'>";
    echo "<thead>
      <tr>
        <th>#</th>
        <th>العنوان</th>
        <th>المجموعة</th>
        <th>الفصل</th>
        <th>المادة</th>
        <th>الصف</th>
        <th style='width:110px'>إجراءات</th>
      </tr>
    </thead>
    <tbody>";
    $i=1;
    foreach($res as $row){
      echo "<tr>";
      echo "<td>".$i++."</td>";
      echo "<td>".htmlspecialchars($row['title'])."</td>";
      echo "<td>".htmlspecialchars($row['group_name'])."</td>";
      echo "<td>".htmlspecialchars($row['semester_name'])."</td>";
      echo "<td>".htmlspecialchars($row['material_name'])."</td>";
      echo "<td>الصف ".((int)$row['class_id'])."</td>";
      echo "<td class='p-1'>
        <div class='d-flex flex-column gap-1 align-items-center'>
          <button class='btn btn-sm btn-outline-primary edit-item' data-id='".intval($row['id'])."'>تعديل</button>
          <button class='btn btn-sm btn-outline-danger delete-item' data-id='".intval($row['id'])."'>حذف</button>
          <button class='btn btn-sm btn-outline-secondary view-item' data-id='".intval($row['id'])."'>مشاهدة</button>
        </div>
      </td>";
      echo "</tr>";
    }
    echo "</tbody></table></div>";
  }
  exit;
}

/* =========== 4. تفاصيل الموضوع =========== */
if($action == 'view_thread' && isset($_GET['id'])){
  $id = intval($_GET['id']);
  $sql = "SELECT threads.*, groups.name as group_name, semesters.name as semester_name, materials.name as material_name, materials.class_id 
          FROM threads
          JOIN groups ON threads.group_id=groups.id
          JOIN semesters ON groups.semester_id=semesters.id
          JOIN materials ON semesters.material_id=materials.id
          WHERE threads.id=$id";
  $row = $conn->query($sql)->fetch_assoc();
  if(!$row){
    echo "<div class='alert alert-danger'>الموضوع غير موجود.</div>";
    exit;
  }
  $files = [];
  $q = $conn->query("SELECT file_path FROM thread_files WHERE thread_id=$id");
  while ($f = $q->fetch_assoc()) $files[] = $f['file_path'];
  ?>
  <div class="card shadow-sm my-3">
    <div class="card-header bg-primary text-white fw-bold">تفاصيل الموضوع</div>
    <div class="card-body">
      <div class="row mb-2">
        <div class="col-4"><b>العنوان:</b> <?= htmlspecialchars($row['title']) ?></div>
        <div class="col-4"><b>المجموعة:</b> <?= htmlspecialchars($row['group_name']) ?></div>
        <div class="col-4"><b>الفصل:</b> <?= htmlspecialchars($row['semester_name']) ?></div>
      </div>
      <div class="row mb-2">
        <div class="col-4"><b>المادة:</b> <?= htmlspecialchars($row['material_name']) ?></div>
        <div class="col-4"><b>الصف:</b> <?= htmlspecialchars($row['class_id']) ?></div>
      </div>
      <div class="mb-3"><b>الوصف:</b> <?= nl2br(htmlspecialchars($row['description'])) ?></div>
      <?php if ($files): ?>
      <div class="mb-2"><b>المرفقات:</b>
        <ul>
        <?php foreach ($files as $f): ?>
          <li>
            <a href="<?= htmlspecialchars($f) ?>" target="_blank"><?= basename($f) ?></a>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php
  exit;
}

/* =========== 5. حذف موضوع مع حذف جميع المرفقات فعلياً =========== */
if($action == 'delete_thread' && $_SERVER['REQUEST_METHOD'] == 'POST'){
  $id = intval($_POST['id'] ?? 0);
  $files = [];
  $q = $conn->query("SELECT file_path FROM thread_files WHERE thread_id=$id");
  while ($f = $q->fetch_assoc()) $files[] = $f['file_path'];
  foreach ($files as $f) {
    $local = __DIR__ . '/' . $f;
    if (is_file($local)) @unlink($local);
  }
  $conn->query("DELETE FROM thread_files WHERE thread_id=$id");
  $conn->query("DELETE FROM threads WHERE id=$id");
  echo "تم حذف الموضوع وجميع مرفقاته نهائياً";
  exit;
}

/* =========== 6. نموذج تعديل الموضوع =========== */
if ($action == 'edit_thread_form' && isset($_GET['id'])){
  $id = intval($_GET['id']);
  $row = $conn->query("SELECT * FROM threads WHERE id=$id")->fetch_assoc();
  if (!$row) {
      echo "<div class='alert alert-danger'>الموضوع غير موجود.</div>";
      exit;
  }
  $groups = $conn->query("SELECT id, name FROM groups ORDER BY name");
  ?>
  <form id="editThreadForm" class="row g-2">
    <div class="col-6">
      <label>العنوان</label>
      <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($row['title']) ?>" required>
    </div>
    <div class="col-6">
      <label>المجموعة</label>
      <select name="group_id" class="form-select" required>
        <?php foreach ($groups as $g): ?>
          <option value="<?= $g['id'] ?>" <?= $row['group_id'] == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <div class="col-12 mt-3">
      <button type="submit" class="btn btn-success">حفظ التعديلات</button>
    </div>
  </form>
  <script>
  $("#editThreadForm").on("submit", function(e){
    e.preventDefault();
    $.post("manage_content_ajax.php", $(this).serialize()+"&action=save_edit_thread", function(msg){
      alert(msg);
      $(".dynamic-search-box select").trigger("change");
    });
  });
  </script>
  <?php
  exit;
}

/* =========== 7. حفظ التعديلات للموضوع =========== */
if ($action == 'save_edit_thread' && $_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = intval($_POST['id'] ?? 0);
  $title = $conn->real_escape_string($_POST['title'] ?? '');
  $group_id = intval($_POST['group_id'] ?? 0);
  $conn->query("UPDATE threads SET title='$title', group_id=$group_id WHERE id=$id");
  echo "تم حفظ التعديلات بنجاح";
  exit;
}

/* =========== 8. إضافة مادة مع فصلين تلقائيين =========== */
if($action=='save_material' && $_SERVER['REQUEST_METHOD']=='POST'){
  $name = trim($_POST['name'] ?? '');
  $class_id = intval($_POST['class_id'] ?? 0);
  $stmt = $conn->prepare("INSERT INTO materials (name,class_id) VALUES (?,?)");
  $stmt->bind_param('si', $name, $class_id);
  $stmt->execute();
  $material_id = $conn->insert_id;
  $conn->query("INSERT INTO semesters (name, material_id) VALUES ('الفصل الأول', $material_id),('الفصل الثاني', $material_id)");
  echo "تم إضافة المادة والفصلين";
  exit;
}

/* =========== 9. إضافة مجموعة =========== */
if($action=='save_group' && $_SERVER['REQUEST_METHOD']=='POST'){
  $name = trim($_POST['name'] ?? '');
  $semester_id = intval($_POST['semester_id'] ?? 0);
  $stmt = $conn->prepare("INSERT INTO groups (name, semester_id) VALUES (?,?)");
  $stmt->bind_param('si', $name, $semester_id);
  $stmt->execute();
  echo "تم إضافة المجموعة";
  exit;
}

/* =========== 10. إضافة موضوع مع رفع ملفات =========== */
if($action=='save_thread' && $_SERVER['REQUEST_METHOD']=='POST'){
  $title = trim($_POST['thread_title'] ?? '');
  $group_id = intval($_POST['group_id'] ?? 0);
  $content_type = $_POST['content_type'] ?? '';
  $description = $_POST['description'] ?? '';

  $stmt = $conn->prepare("INSERT INTO threads (title, group_id, content_type, description) VALUES (?, ?, ?, ?)");
  $stmt->bind_param('siss', $title, $group_id, $content_type, $description);
  $stmt->execute();
  $thread_id = $conn->insert_id;

  // رفع الملفات
  if (!empty($_FILES['file_upload']['name'][0])) {
      $allowed_ext = ['pdf','jpg','jpeg','png','gif','doc','docx','mp4','mov','avi','webp'];
      $target_dir = __DIR__."/uploads/";
      if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);

      foreach ($_FILES['file_upload']['name'] as $i => $name) {
          if (empty($name)) continue;
          $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
          if (!in_array($ext, $allowed_ext)) {
              echo json_encode(['status'=>'error', 'msg'=>"صيغة الملف غير مدعومة للملف: " . htmlspecialchars($name)], JSON_UNESCAPED_UNICODE);
              exit;
          }
          $filename = uniqid()."_".basename($name);
          $target_file = $target_dir . $filename;
          if(move_uploaded_file($_FILES['file_upload']['tmp_name'][$i], $target_file)){
              $file_path = "uploads/".$filename;
              $conn->query("INSERT INTO thread_files (thread_id, file_path) VALUES ($thread_id, '$file_path')");
          } else {
              echo json_encode(['status'=>'error', 'msg'=>"حدث خطأ أثناء رفع الملف: ".htmlspecialchars($name)], JSON_UNESCAPED_UNICODE);
              exit;
          }
      }
  }

  // روابط خارجية (إن وجدت)
  if (!empty($_POST['external_url'])) {
      $external_url = trim($_POST['external_url']);
      $conn->query("INSERT INTO thread_files (thread_id, file_path) VALUES ($thread_id, '$external_url')");
  }

  echo json_encode(['status'=>'success', 'msg'=>"تم إضافة الموضوع"], JSON_UNESCAPED_UNICODE);
  exit;
}

/* =========== 11. تعديل اسم مادة/مجموعة/موضوع =========== */
if($action == 'edit' && $_SERVER['REQUEST_METHOD']=='POST') {
  $type = $_POST['type'] ?? '';
  $id = intval($_POST['id'] ?? 0);
  $value = trim($_POST['value'] ?? '');
  if($type === 'material') {
    $conn->query("UPDATE materials SET name='$value' WHERE id=$id");
    echo "تم التعديل بنجاح";
  }
  if($type === 'group') {
    $conn->query("UPDATE groups SET name='$value' WHERE id=$id");
    echo "تم التعديل بنجاح";
  }
  if($type === 'thread') {
    $conn->query("UPDATE threads SET title='$value' WHERE id=$id");
    echo "تم التعديل بنجاح";
  }
  exit;
}

/* =========== 12. حذف مادة/مجموعة/موضوع من القائمة الجانبية/البحث =========== */
if($action == 'delete' && $_SERVER['REQUEST_METHOD']=='POST') {
  $type = $_POST['type'] ?? '';
  $id = intval($_POST['id'] ?? 0);
  if($type === 'material') {
    $conn->query("DELETE FROM materials WHERE id=$id");
    echo "تم حذف المادة بنجاح";
  }
  if($type === 'group') {
    $conn->query("DELETE FROM groups WHERE id=$id");
    echo "تم حذف المجموعة بنجاح";
  }
  if($type === 'thread') {
    $files = [];
    $q = $conn->query("SELECT file_path FROM thread_files WHERE thread_id=$id");
    while ($f = $q->fetch_assoc()) $files[] = $f['file_path'];
    foreach ($files as $f) {
      $local = __DIR__ . '/' . $f;
      if (is_file($local)) @unlink($local);
    }
    $conn->query("DELETE FROM thread_files WHERE thread_id=$id");
    $conn->query("DELETE FROM threads WHERE id=$id");
    echo "تم حذف الموضوع بنجاح";
  }
  exit;
}

/* =========== 13. عرض المزيد من المواضيع للقائمة الجانبية =========== */
if($action == 'more_threads') {
  $group_id = intval($_GET['group_id'] ?? 0);
  $offset = intval($_GET['offset'] ?? 0);
  $threads = $conn->query("SELECT id, title FROM threads WHERE group_id=$group_id ORDER BY id DESC LIMIT 10 OFFSET $offset");
  $count = 0;
  foreach($threads as $t){
    echo '<div class="d-flex align-items-center justify-content-between thread-title mb-1">
            <span>'.htmlspecialchars($t['title']??"").'</span>
            <span class="d-flex flex-column gap-1 align-items-end">
              <button class="btn btn-sm btn-outline-primary edit-item d-flex align-items-center gap-1" data-type="thread" data-id="'.intval($t['id']).'">
                <i class="bi bi-pencil-square"></i> تعديل
              </button>
              <button class="btn btn-sm btn-outline-danger delete-item d-flex align-items-center gap-1" data-type="thread" data-id="'.intval($t['id']).'">
                <i class="bi bi-trash"></i> حذف
              </button>
              <button class="btn btn-sm btn-outline-secondary view-item d-flex align-items-center gap-1" data-type="thread" data-id="'.intval($t['id']).'">
                <i class="bi bi-eye"></i> مشاهدة
              </button>
            </span>
          </div>';
    $count++;
  }
  $cnt = $conn->query("SELECT COUNT(*) c FROM threads WHERE group_id=$group_id")->fetch_assoc();
  if($cnt['c'] > $offset + $count) {
    echo '<button class="btn btn-link p-0" onclick="loadMoreThreads('.$group_id.',this)">عرض المزيد...</button>';
  }
  exit;
}
?>