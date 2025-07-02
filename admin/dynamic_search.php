<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$conn = new mysqli('localhost', 'azmi3', 'Tvvcrtv1610@', 'azmi3');
$conn->set_charset('utf8mb4');
$classes = [];
for($i=1;$i<=12;$i++) $classes[] = ['id'=>$i, 'name'=>"الصف $i"];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>لوحة إدارة المحتوى</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <style>
    body { background: #f8fafd; font-family: 'Cairo', sans-serif; direction: rtl; margin:0; padding:0;}
    .header-simple {
      background: #25316D;
      color: #fff;
      padding: 18px 0 12px 0;
      text-align: center;
      font-size: 26px;
      font-weight: bold;
      letter-spacing: 2px;
      box-shadow: 0 2px 6px #0001;
    }
    .breadcrumb-bar {
      background: #EDF2FA;
      border-radius: 7px;
      padding: 9px 24px;
      margin: 18px 0 16px 0;
      font-size: 14px;
      font-weight: 500;
      box-shadow: 0 1px 4px #0001;
      display: flex; align-items: center;
    }
    .breadcrumb-bar .breadcrumb {
      margin-bottom: 0;
      background: none;
      padding: 0;
    }
    .main-layout {
      display: flex;
      flex-direction: row-reverse;
      min-height: 100vh;
    }
    .sidebar {
      width: 360px;
      background: #1e3a8a;
      color: #fff;
      min-height: 100vh;
      padding: 30px 20px 30px 10px;
      position: fixed;
      right: 0;
      top: 0;
      bottom: 0;
      overflow-y: auto;
      border-left: 3px solid #f1f1f1;
      box-shadow: -2px 0 10px 0 rgba(0,0,0,0.05);
      z-index: 10;
      flex-shrink: 0;
    }
    .main-area {
      flex: 1;
      margin-right: 360px;
      padding: 0;
      min-width: 0;
    }
    .container-fluid {
      max-width: 1800px;
      padding: 0 25px;
    }
    .dynamic-search-box {
      background: #fff;
      border-radius: 9px;
      box-shadow: 0 3px 10px #0001;
      padding: 18px 22px 12px 22px;
      margin-bottom: 0;
      margin-top: 10px;
    }
    .dynamic-search-box .form-select, .dynamic-search-box .form-control {
      min-width: 120px;
      font-size: 15px;
    }
    #searchResults {
      background: #fff;
      border-radius: 9px;
      box-shadow: 0 2px 7px #0001;
      margin-bottom: 25px;
      margin-top: 0;
      padding: 18px 22px 18px 22px;
      min-height: 60px;
      transition: min-height 0.2s;
    }
    .alert-fixed { position:fixed; top:30px; left:30px; z-index:9999; min-width:200px; }
    .form-section { margin-bottom: 32px; }
    .thread-title { font-size: 13px; color: #ffe082; }
    .footer-simple {
      background: #25316D;
      color: #fff;
      text-align: center;
      padding: 16px 0;
      margin-top: 50px;
      font-size: 15px;
      letter-spacing: 1px;
      box-shadow: 0 -2px 6px #0001;
    }
    @media (max-width: 991px) {
      .main-layout { flex-direction: column; }
      .sidebar {
        width: 100%;
        min-height: auto;
        position: static;
        border-left: none;
        box-shadow: none;
        padding: 16px 10px 12px 10px;
      }
      .main-area { margin-right: 0; }
      .container-fluid { padding: 0 5px;}
      .dynamic-search-box, #searchResults { padding: 11px 3vw; }
    }
    @media (max-width: 767px) {
      .dynamic-search-box, #searchResults {padding: 8px 1vw;}
    }
  </style>
</head>
<body>
  <div class="header-simple">
    لوحة إدارة المحتوى
  </div>

  <div class="main-layout">
    <div class="sidebar">
      <div class="section-title mb-2" style="color: #ffe082; font-weight: bold; font-size: 20px;">كل المواد والمجموعات والمواضيع</div>
      <input type="text" id="sidebarSearch" class="form-control mb-3" placeholder="بحث...">
      <div id="sidebarList"></div>
    </div>
    <div class="main-area">
      <div class="container-fluid">
        <!-- المسار Breadcrumb -->
        <nav class="breadcrumb-bar" aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#" style="color:#1e3a8a; text-decoration:none;">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">إدارة المحتوى</li>
          </ol>
        </nav>

        <!-- مربع البحث الديناميكي -->
        <div class="dynamic-search-box">
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
            <!-- زر البحث مخفي لأن البحث تلقائي -->
            <div class="col-2 col-sm-2 d-none">
              <button type="submit" class="btn btn-primary w-100">بحث</button>
            </div>
          </form>
        </div>
        <!-- نتائج البحث تظهر مباشرة أسفل البحث -->
        <div id="searchResults"></div>
      </div>

      <div class="content-area">
        <div id="alertBox" class="alert alert-success alert-fixed d-none"></div>
        <div class="form-section">
          <h5>إضافة مادة</h5>
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
        <div class="form-section">
          <h5>إضافة مجموعة</h5>
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
        <div class="form-section">
          <h5>إضافة موضوع</h5>
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
              <input type="file" name="file_upload" class="form-control" id="file_upload">
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
      <div class="footer-simple">
        جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?> | لوحة إدارة المحتوى
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function showAlert(msg) {
      $("#alertBox").text(msg).removeClass('d-none').fadeIn();
      setTimeout(function() { $("#alertBox").fadeOut(); }, 3000);
    }
    function loadSidebar() {
      $.get("manage_content_ajax.php", {action:'sidebar_list'}, function(data){
        $("#sidebarList").html(data);
      });
    }
    function resetSelect($select, placeholder) {
      $select.html('<option value="">' + placeholder + '</option>').prop('disabled', true);
    }

    // === بحث ديناميكي تلقائي ===
    function triggerDynamicSearch() {
      let params = {
        class_id: $("#searchClass").val(),
        material_id: $("#searchMaterial").val(),
        semester_id: $("#searchSemester").val(),
        group_id: $("#searchGroup").val(),
        thread_id: $("#searchThread").val(),
        action: "search_results"
      };
      if (!params.class_id) {
        $("#searchResults").html('<div class="alert alert-info">اختر الصف لعرض النتائج</div>');
        return;
      }
      $("#searchResults").html('<div class="text-center my-3"><div class="spinner-border text-primary"></div></div>');
      $.get("manage_content_ajax.php", params, function(data){
        $("#searchResults").html(data);
      });
    }

    $(document).ready(function(){
      loadSidebar();

      // بحث الشريط الجانبي
      $("#sidebarSearch").on("input", function(){
        let q = $(this).val().trim();
        $("#sidebarList .accordion-item").show();
        if(q) {
          $("#sidebarList .accordion-item").each(function(){
            if($(this).text().indexOf(q) === -1) $(this).hide();
          });
        }
      });

      // نموذج البحث الديناميكي
      $("#searchClass").on("change", function() {
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
        triggerDynamicSearch(); // بحث تلقائي بعد تغيير الصف
      });
      $("#searchMaterial").on("change", function() {
        let material_id = $(this).val();
        resetSelect($("#searchSemester"), "اختر الفصل");
        resetSelect($("#searchGroup"), "اختر المجموعة");
        resetSelect($("#searchThread"), "اختر الموضوع");
        if(material_id) {
          $.get("manage_content_ajax.php", {action:'list_semesters', material_id}, function(data){
            $("#searchSemester").html('<option value="">اختر الفصل</option>'+data).prop('disabled', false);
          });
        }
        triggerDynamicSearch();
      });
      $("#searchSemester").on("change", function() {
        let semester_id = $(this).val();
        resetSelect($("#searchGroup"), "اختر المجموعة");
        resetSelect($("#searchThread"), "اختر الموضوع");
        if(semester_id) {
          $.get("manage_content_ajax.php", {action:'list_groups', semester_id}, function(data){
            $("#searchGroup").html('<option value="">اختر المجموعة</option>'+data).prop('disabled', false);
          });
        }
        triggerDynamicSearch();
      });
      $("#searchGroup").on("change", function() {
        let group_id = $(this).val();
        resetSelect($("#searchThread"), "اختر الموضوع");
        if(group_id) {
          $.get("manage_content_ajax.php", {action:'list_threads', group_id}, function(data){
            $("#searchThread").html('<option value="">اختر الموضوع</option>'+data).prop('disabled', false);
          });
        }
        triggerDynamicSearch();
      });
      $("#searchThread").on("change", function() {
        triggerDynamicSearch();
      });

      // منع الإرسال الافتراضي للفورم (لأن البحث تلقائي)
      $("#dynamicSearchForm").on("submit", function(e){ e.preventDefault(); });

      // بحث تلقائي عند تحميل الصفحة إذا كان هناك صف محدد
      if ($("#searchClass").val()) triggerDynamicSearch();

      // باقي الأكواد (إضافة، تعديل، حذف ...) كما هي
      // إضافة مادة
      $("#addMaterialForm").submit(function(e){
        e.preventDefault();
        let class_id = $("#selectClassForMaterial").val();
        let name = $(this).find("[name='material_name']").val();
        $.post("manage_content_ajax.php", {action:'save_material', name, class_id}, function(res){
          showAlert(res);
          loadSidebar();
          $("#addMaterialForm")[0].reset();
          $("#selectClassForGroup, #selectClassForThread").trigger('change');
        });
      });

      // إضافة مجموعة
      $("#selectClassForGroup").change(function(){
        let class_id = $(this).val();
        $.get("manage_content_ajax.php", {action:'list_materials', class_id}, function(data){
          $("#selectMaterialForGroup").html('<option value="">المادة ...</option>'+data);
          $("#selectSemesterForGroup").html('<option value="">الفصل ...</option>');
        });
      });
      $("#selectMaterialForGroup").change(function(){
        let material_id = $(this).val();
        $.get("manage_content_ajax.php", {action:'list_semesters', material_id}, function(data){
          $("#selectSemesterForGroup").html('<option value="">الفصل ...</option>'+data);
        });
      });
      $("#addGroupForm").submit(function(e){
        e.preventDefault();
        let material_id = $("#selectMaterialForGroup").val();
        let semester_id = $("#selectSemesterForGroup").val();
        let name = $(this).find("[name='group_name']").val();
        $.post("manage_content_ajax.php", {action:'save_group', name, semester_id}, function(res){
          showAlert(res);
          loadSidebar();
          $("#addGroupForm")[0].reset();
          $("#selectClassForGroup, #selectMaterialForGroup").trigger('change');
        });
      });

      // إضافة موضوع
      $("#selectClassForThread").change(function(){
        let class_id = $(this).val();
        $.get("manage_content_ajax.php", {action:'list_materials', class_id}, function(data){
          $("#selectMaterialForThread").html('<option value="">المادة ...</option>'+data);
          $("#selectSemesterForThread").html('<option value="">الفصل ...</option>');
          $("#selectGroupForThread").html('<option value="">المجموعة ...</option>');
        });
      });
      $("#selectMaterialForThread").change(function(){
        let material_id = $(this).val();
        $.get("manage_content_ajax.php", {action:'list_semesters', material_id}, function(data){
          $("#selectSemesterForThread").html('<option value="">الفصل ...</option>'+data);
          $("#selectGroupForThread").html('<option value="">المجموعة ...</option>');
        });
      });
      $("#selectSemesterForThread").change(function(){
        let semester_id = $(this).val();
        $.get("manage_content_ajax.php", {action:'list_groups', semester_id}, function(data){
          $("#selectGroupForThread").html('<option value="">المجموعة ...</option>'+data);
        });
      });
      $("#addThreadForm").submit(function(e){
        e.preventDefault();
        let formData = new FormData(this);

        let fileInput = document.getElementById('file_upload');
        let fileSelected = fileInput && fileInput.files && fileInput.files.length > 0;
        let urlEntered = !!formData.get('external_url');

        // يمنع رفع ملف ورابط معًا
        if (fileSelected && urlEntered && formData.get('external_url').trim() != '') {
          alert('يرجى رفع ملف أو إدخال رابط فقط وليس الاثنين معًا.');
          return;
        }

        // أهم نقطة: إضافة action ليصل للباك إند
        formData.append('action', 'save_thread');

        $.ajax({
          url: "manage_content_ajax.php",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function(res){
            showAlert(res);
            loadSidebar();
            $("#addThreadForm")[0].reset();
            $("#selectClassForThread, #selectMaterialForThread, #selectSemesterForThread").trigger('change');
          }
        });
      });

      // حذف
      $(document).on('click', '.delete-item', function(){
        if(!confirm("تأكيد الحذف؟")) return;
        let type = $(this).data('type'), id = $(this).data('id');
        $.post("manage_content_ajax.php", {action:'delete', type, id}, function(res){
          showAlert(res);
          loadSidebar();
          $("#selectClassForGroup, #selectClassForThread").trigger('change');
        });
      });

      // تعديل
      $(document).on("click", ".edit-item", function(e){
        e.preventDefault();
        let type = $(this).data("type");
        let id = $(this).data("id");
        let oldVal = $(this).closest(".accordion-header, .thread-title").find("span:first").text().trim();
        $("#editType").val(type);
        $("#editId").val(id);
        $("#editValue").val(oldVal);
        $("#editLabel").text("تعديل " + (type === "material" ? "اسم المادة" : type === "group" ? "اسم المجموعة" : "عنوان الموضوع"));
        $("#editModalTitle").text("تعديل " + (type === "material" ? "مادة" : type === "group" ? "مجموعة" : "موضوع"));
        var modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
      });

      // حفظ التعديل
      $("#editForm").submit(function(e){
        e.preventDefault();
        $.post("manage_content_ajax.php", {
          action: "edit",
          type: $("#editType").val(),
          id: $("#editId").val(),
          value: $("#editValue").val()
        }, function(res){
          showAlert(res);
          loadSidebar();
          var modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
          if(modal) modal.hide();
        });
      });

      // زر عرض المزيد
      window.loadMoreThreads = function(group_id, btn) {
        $.get("manage_content_ajax.php", {action:"more_threads", group_id:group_id, offset:10}, function(data){
          $(btn).before(data);
          $(btn).remove();
        });
      }
    });
  </script>

  <!-- نافذة التعديل (Modal) -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="editForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalTitle">تعديل</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="type" id="editType">
          <input type="hidden" name="id" id="editId">
          <div class="mb-3">
            <label class="form-label" id="editLabel"></label>
            <input type="text" class="form-control" name="value" id="editValue" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">حفظ</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>