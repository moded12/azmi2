<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli('localhost', 'azmi3', 'Tvvcrtv1610@', 'azmi3');
$conn->set_charset('utf8mb4');

// جلب الصفوف
$classes = [];
$q = $conn->query("SELECT DISTINCT class_id FROM materials ORDER BY class_id");
while($row = $q->fetch_assoc()) $classes[] = $row['class_id'];

// جلب المواد
$materials = [];
$q = $conn->query("SELECT id, name FROM materials ORDER BY id");
while($row = $q->fetch_assoc()) $materials[] = $row;

// جلب الفصول
$semesters = [];
$q = $conn->query("SELECT DISTINCT name FROM semesters ORDER BY id");
while($row = $q->fetch_assoc()) $semesters[] = $row['name'];

// جلب المجموعات
$groups = [];
$q = $conn->query("SELECT id, name FROM groups ORDER BY id");
while($row = $q->fetch_assoc()) $groups[] = $row;
?>
<div class="filter-bar shadow-sm mb-3">
  <select id="classFilter" class="form-select w-auto">
    <option value="">كل الصفوف</option>
    <?php foreach($classes as $c): ?>
      <option value="<?php echo htmlspecialchars($c); ?>">الصف <?php echo htmlspecialchars($c); ?></option>
    <?php endforeach; ?>
  </select>
  <select id="materialFilter" class="form-select w-auto">
    <option value="">كل المواد</option>
    <?php foreach($materials as $m): ?>
      <option value="<?php echo htmlspecialchars($m['name']); ?>"><?php echo htmlspecialchars($m['name']); ?></option>
    <?php endforeach; ?>
  </select>
  <select id="semesterFilter" class="form-select w-auto">
    <option value="">كل الفصول</option>
    <?php foreach($semesters as $s): ?>
      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
    <?php endforeach; ?>
  </select>
  <select id="groupFilter" class="form-select w-auto">
    <option value="">كل المجموعات</option>
    <?php foreach($groups as $g): ?>
      <option value="<?php echo htmlspecialchars($g['name']); ?>"><?php echo htmlspecialchars($g['name']); ?></option>
    <?php endforeach; ?>
  </select>
  <input id="searchInput" class="search-input" type="text" placeholder="ابحث عن موضوع أو وصف ..." />
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // البحث تلقائي عند تغيير أي قائمة أو عند الكتابة في مربع البحث
  ['classFilter','materialFilter','semesterFilter','groupFilter','searchInput'].forEach(function(id){
    document.getElementById(id).addEventListener(
      id==='searchInput' ? 'input' : 'change',
      function(){ if(typeof loadThreads === 'function') loadThreads(); }
    );
  });
});
</script>
<style>
.filter-bar { background: #e3f2fd; border-radius: 12px; margin-top: -30px; padding: 16px 24px 10px 24px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; justify-content: flex-start;}
.search-input { min-width: 200px; max-width: 280px; border-radius: 7px; border: 1px solid #b5d0ee; padding: 6px 12px;}
@media (max-width: 900px) {.filter-bar { flex-direction: column; align-items: stretch; padding: 12px 10px 10px 10px;} }
</style>