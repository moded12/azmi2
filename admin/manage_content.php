<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$conn = new mysqli('localhost', 'azmi3', 'Tvvcrtv1610@', 'azmi3');
$conn->set_charset('utf8mb4');
$classes = [];
$classes = [];
$result = $conn->query("SELECT id, name FROM classes ORDER BY id ASC");
while($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>لوحة إدارة المحتوى</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="./style.css">
</head>
<body>
  <div class="header-simple">
    لوحة إدارة المحتوى
  </div>
  <div class="dynamic-search-box mt-4 mb-3">
    <form id="dynamicSearchForm" class="row g-2 align-items-end">
      <div class="col-2 col-sm-2">
        <label class="form-label">الصف</label>
        <select id="searchClass" class="form-select" required>
          <option value="">اختر الصف</option>
          <?php foreach($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-2 col-sm-2">
        <label class="form-label">المادة</label>
        <select id="searchMaterial" class="form-select" disabled>
          <option value="">اختر المادة</option>
        </select>
      </div>
      <div class="col-2 col-sm-2">
        <label class="form-label">الفصل</label>
        <select id="searchSemester" class="form-select" disabled>
          <option value="">اختر الفصل</option>
        </select>
      </div>
      <div class="col-2 col-sm-2">
        <label class="form-label">المجموعة</label>
        <select id="searchGroup" class="form-select" disabled>
          <option value="">اختر المجموعة</option>
        </select>
      </div>
      <div class="col-2 col-sm-2">
        <label class="form-label">الموضوع</label>
        <select id="searchThread" class="form-select" disabled>
          <option value="">اختر الموضوع</option>
        </select>
      </div>
    </form>
  </div>
  <div class="main-layout">
    <div class="sidebar">
      <div class="section-title mb-2" style="color: #ffe082; font-weight: bold; font-size: 20px;">خيارات الإدارة</div>
      <div class="list-group mb-4" id="sideLinks">
        <a class="list-group-item list-group-item-action" data-form="material" href="#">إضافة مادة</a>
        <a class="list-group-item list-group-item-action" data-form="semester" href="#">إضافة فصل</a>
        <a class="list-group-item list-group-item-action" data-form="group" href="#">إضافة مجموعة</a>
        <a class="list-group-item list-group-item-action" data-form="thread" href="#">إضافة موضوع</a>
      </div>
      <div id="sidebarList"></div>
    </div>
    <div class="main-area">
      <div class="container-fluid">
        <nav class="breadcrumb-bar" aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#" style="color:#1e3a8a; text-decoration:none;">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page" id="breadcrumbLabel">إدارة المحتوى</li>
          </ol>
        </nav>
        <div id="alertBox" class="alert alert-success alert-fixed d-none"></div>
        <div id="mainContentPage" class="pt-2">
          <div class="text-center text-secondary fs-4 my-5">اختر ما تريد إضافته من القائمة الجانبية 👈</div>
        </div>
        <div id="searchResults"></div>
      </div>
      <div class="footer-simple">
        جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?> | لوحة إدارة المحتوى
      </div>
    </div>
  </div>

  <!-- جميع النماذج للإضافة -->
  <div id="allForms" style="display:none">
    <div id="form-material">
      <h5 class="mb-3">إضافة مادة</h5>
      <form id="addMaterialForm" class="row g-2">
        <div class="col-4">
          <select name="class_id" class="form-select" id="selectClassForMaterial" required>
            <option value="">اختر الصف</option>
            <?php foreach($classes as $c): ?>
              <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-5"><input type="text" name="material_name" class="form-control" placeholder="اسم المادة..." required></div>
        <div class="col-3"><button type="submit" class="btn btn-success w-100">إضافة مادة</button></div>
      </form>
    </div>
    <div id="form-semester">
      <h5 class="mb-3">إضافة فصل</h5>
      <form id="addSemesterForm" class="row g-2">
        <div class="col-7"><input type="text" name="semester_name" class="form-control" placeholder="اسم الفصل..." required></div>
        <div class="col-5"><button type="submit" class="btn btn-success w-100">إضافة فصل</button></div>
      </form>
    </div>
    <div id="form-group">
      <h5 class="mb-3">إضافة مجموعة</h5>
      <form id="addGroupForm" class="row g-2">
        <div class="col-3">
          <select id="selectClassForGroup" class="form-select" required>
            <option value="">الصف</option>
            <?php foreach($classes as $c): ?>
              <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-3">
          <select id="selectMaterialForGroup" name="material_id" class="form-select" required>
            <option value="">المادة</option>
          </select>
        </div>
        <div class="col-3">
          <select id="selectSemesterForGroup" name="semester_id" class="form-select" required>
            <option value="">الفصل</option>
          </select>
        </div>
        <div class="col-3">
          <input type="text" name="group_name" class="form-control" placeholder="اسم المجموعة..." required>
        </div>
        <div class="col-12 mt-2">
          <button type="submit" class="btn btn-primary w-100">إضافة مجموعة</button>
        </div>
      </form>
    </div>
    <div id="form-thread">
      <h5 class="mb-3">إضافة موضوع</h5>
      <form id="addThreadForm" class="row g-2" enctype="multipart/form-data">
        <div class="col-2">
          <select id="selectClassForThread" class="form-select" required>
            <option value="">الصف</option>
            <?php foreach($classes as $c): ?>
              <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-3">
          <select id="selectMaterialForThread" class="form-select" required>
            <option value="">المادة</option>
          </select>
        </div>
        <div class="col-2">
          <select id="selectSemesterForThread" class="form-select" required>
            <option value="">الفصل</option>
          </select>
        </div>
        <div class="col-2">
          <select id="selectGroupForThread" name="group_id" class="form-select" required>
            <option value="">المجموعة</option>
          </select>
        </div>
        <div class="col-3">
          <input type="text" name="thread_title" class="form-control" placeholder="عنوان الموضوع..." required>
        </div>
        <div class="col-2 mt-2">
          <select name="content_type" class="form-select" required>
            <option value="">نوع المحتوى</option>
            <option value="pdf">PDF</option>
            <option value="image">صورة</option>
            <option value="url">رابط خارجي</option>
            <option value="docs">Word/Docs</option>
            <option value="video">فيديو</option>
            <option value="other">غير ذلك</option>
          </select>
        </div>
        <div class="col-3 mt-2">
          <input type="text" name="description" class="form-control" placeholder="وصف الموضوع..." required>
        </div>
        <div class="col-3 mt-2">
          <input type="file" name="file_upload[]" class="form-control" id="file_upload" multiple>
        </div>
        <div class="col-3 mt-2">
          <input type="url" name="external_url" class="form-control" placeholder="أو أدخل رابط خارجي">
        </div>
        <div class="col-1 mt-2">
          <button type="submit" class="btn btn-warning w-100">إضافة موضوع</button>
        </div>
      </form>
    </div>
  </div>

 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  // البحث الديناميكي: تحديث تلقائي عند كل تغيير
  function resetSelect($select, placeholder) {
    $select.html('<option value="">' + placeholder + '</option>').prop('disabled', true);
  }
  function doDynamicSearch() {
    let params = {
      class_id: $("#searchClass").val(),
      material_id: $("#searchMaterial").val(),
      semester_id: $("#searchSemester").val(),
      group_id: $("#searchGroup").val(),
      thread_id: $("#searchThread").val(),
      action: "search_results"
    };
    $.get("manage_content_ajax.php", params, function(data){
      $("#searchResults").html(data);
      bindSearchActions();
    });
  }
  // الإجراءات الحديثة: حذف/تعديل/مشاهدة - متوافقة مع أزرار النتائج
  function bindSearchActions() {
    // حذف موضوع
    $('#searchResults').off('click', '.delete-item').on('click', '.delete-item', function(){
      if(confirm('تأكيد الحذف؟')) {
        const threadId = $(this).data('id');
        $.post('manage_content_ajax.php', {action:'delete_thread', id:threadId}, function(msg){
          alert(msg);
          doDynamicSearch();
        });
      }
    });
    // تعديل موضوع
    $('#searchResults').off('click', '.edit-item').on('click', '.edit-item', function(){
      const threadId = $(this).data('id');
      $.get('manage_content_ajax.php', {action:'edit_thread_form', id:threadId}, function(html){
        $("#searchResults").html(html);
        // عند عرض نموذج التعديل أربط حفظ التعديلات داخله تلقائياً
        $("#editThreadForm").on("submit", function(e){
          e.preventDefault();
          $.post("manage_content_ajax.php", $(this).serialize()+"&action=save_edit_thread", function(msg){
            alert(msg);
            doDynamicSearch();
          });
        });
      });
    });
    // مشاهدة موضوع
    $('#searchResults').off('click', '.view-item').on('click', '.view-item', function(){
      const threadId = $(this).data('id');
      $.get('manage_content_ajax.php', {action:'view_thread', id:threadId}, function(html){
        $("#searchResults").html(html);
      });
    });
  }
  $(document).ready(function(){
    // البحث الديناميكي عند تغيير أي فلتر
    $("#searchClass").change(function() {
      let class_id = $(this).val();
      resetSelect($("#searchMaterial"), "اختر المادة");
      resetSelect($("#searchSemester"), "اختر الفصل");
      resetSelect($("#searchGroup"), "اختر المجموعة");
      resetSelect($("#searchThread"), "اختر الموضوع");
      if(class_id) {
        $.get("manage_content_ajax.php", {action:'list_materials', class_id}, function(data){
          $("#searchMaterial").html('<option value="">اختر المادة</option>'+data).prop('disabled', false);
        });
      }
      doDynamicSearch();
    });
    $("#searchMaterial").change(function() {
      let material_id = $(this).val();
      resetSelect($("#searchSemester"), "اختر الفصل");
      resetSelect($("#searchGroup"), "اختر المجموعة");
      resetSelect($("#searchThread"), "اختر الموضوع");
      if(material_id) {
        $.get("manage_content_ajax.php", {action:'list_semesters', material_id}, function(data){
          $("#searchSemester").html('<option value="">اختر الفصل</option>'+data).prop('disabled', false);
        });
      }
      doDynamicSearch();
    });
    $("#searchSemester").change(function() {
      let semester_id = $(this).val();
      resetSelect($("#searchGroup"), "اختر المجموعة");
      resetSelect($("#searchThread"), "اختر الموضوع");
      if(semester_id) {
        $.get("manage_content_ajax.php", {action:'list_groups', semester_id}, function(data){
          $("#searchGroup").html('<option value="">اختر المجموعة</option>'+data).prop('disabled', false);
        });
      }
      doDynamicSearch();
    });
    $("#searchGroup").change(function() {
      let group_id = $(this).val();
      resetSelect($("#searchThread"), "اختر الموضوع");
      if(group_id) {
        $.get("manage_content_ajax.php", {action:'list_threads', group_id}, function(data){
          $("#searchThread").html('<option value="">اختر الموضوع</option>'+data).prop('disabled', false);
        });
      }
      doDynamicSearch();
    });
    $("#searchThread").change(function() {
      doDynamicSearch();
    });

    // أول تحميل
    doDynamicSearch();

    // (روابط الإدارة)
    $(document).on("click", "#sideLinks .list-group-item", function(e){
      e.preventDefault();
      $("#sideLinks .list-group-item").removeClass("active");
      $(this).addClass("active");
      var form = $(this).data("form");
      var title = $(this).text();
      $("#breadcrumbLabel").text(title);
      $("#mainContentPage").html($("#form-" + form).html());
      bindForms();
    });
  });

  // الأحداث للنماذج المنسوخة
  function bindForms() {
    // إضافة مادة
    $("#addMaterialForm").off().on("submit", function(e){
      e.preventDefault();
      let class_id = $("#selectClassForMaterial").val();
      let name = $(this).find("[name='material_name']").val();
      $.post("manage_content_ajax.php", {action:'save_material', name, class_id}, function(res){
        showAlert(res);
        $("#addMaterialForm")[0].reset();
      });
    });
    // إضافة فصل
    $("#addSemesterForm").off().on("submit", function(e){
      e.preventDefault();
      let name = $(this).find("[name='semester_name']").val();
      $.post("manage_content_ajax.php", {action:'save_semester', name}, function(res){
        showAlert(res);
        $("#addSemesterForm")[0].reset();
      });
    });
    // إضافة مجموعة
    $("#selectClassForGroup").off().on("change", function(){
      let class_id = $(this).val();
      $.get("manage_content_ajax.php", {action:'list_materials', class_id}, function(data){
        $("#selectMaterialForGroup").html('<option value="">المادة ...</option>'+data);
        $("#selectSemesterForGroup").html('<option value="">الفصل ...</option>');
      });
    });
    $("#selectMaterialForGroup").off().on("change", function(){
      let material_id = $(this).val();
      $.get("manage_content_ajax.php", {action:'list_semesters', material_id}, function(data){
        $("#selectSemesterForGroup").html('<option value="">الفصل ...</option>'+data);
      });
    });
    $("#addGroupForm").off().on("submit", function(e){
      e.preventDefault();
      let material_id = $("#selectMaterialForGroup").val();
      let semester_id = $("#selectSemesterForGroup").val();
      let name = $(this).find("[name='group_name']").val();
      $.post("manage_content_ajax.php", {action:'save_group', name, semester_id}, function(res){
        showAlert(res);
        $("#addGroupForm")[0].reset();
      });
    });
    // إضافة موضوع
    $("#selectClassForThread").off().on("change", function(){
      let class_id = $(this).val();
      $.get("manage_content_ajax.php", {action:'list_materials', class_id}, function(data){
        $("#selectMaterialForThread").html('<option value="">المادة ...</option>'+data);
        $("#selectSemesterForThread").html('<option value="">الفصل ...</option>');
        $("#selectGroupForThread").html('<option value="">المجموعة ...</option>');
      });
    });
    $("#selectMaterialForThread").off().on("change", function(){
      let material_id = $(this).val();
      $.get("manage_content_ajax.php", {action:'list_semesters', material_id}, function(data){
        $("#selectSemesterForThread").html('<option value="">الفصل ...</option>'+data);
        $("#selectGroupForThread").html('<option value="">المجموعة ...</option>');
      });
    });
    $("#selectSemesterForThread").off().on("change", function(){
      let semester_id = $(this).val();
      $.get("manage_content_ajax.php", {action:'list_groups', semester_id}, function(data){
        $("#selectGroupForThread").html('<option value="">المجموعة ...</option>'+data);
      });
    });
    $("#addThreadForm").off().on("submit", function(e){
      e.preventDefault();
      let formData = new FormData(this);
      let fileInput = document.getElementById('file_upload');
      let fileSelected = fileInput && fileInput.files && fileInput.files.length > 0;
      let urlEntered = !!formData.get('external_url');
      if (fileSelected && urlEntered && formData.get('external_url').trim() != '') {
        alert('يرجى رفع ملف أو إدخال رابط فقط وليس الاثنين معًا.');
        return;
      }
      formData.append('action', 'save_thread');
      $.ajax({
        url: "manage_content_ajax.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(res){
          showAlert(res);
          $("#addThreadForm")[0].reset();
        }
      });
    });
  }
  function showAlert(msg) {
    $("#alertBox").text(msg).removeClass('d-none').fadeIn();
    setTimeout(function() { $("#alertBox").fadeOut(); }, 3000);
  }
</script>
</body>
</html>