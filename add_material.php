<?php
require_once "includes/db.php";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المواد</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .action-btns button { margin-left: 5px; }
        #debug {background:#f1f1f1;color:#a00;font-size:13px;padding:6px 10px;margin-bottom:8px;direction:ltr;}
    </style>
</head>
<body>
<div class="container">
    <h3 class="mt-4 mb-3">إدارة المواد الدراسية</h3>
    <div id="debug"></div>
    <form id="material-form" class="row g-2 mb-4" autocomplete="off">
        <input type="hidden" id="material_id" name="material_id" value="">
        <div class="col-md-6">
            <label for="material_name" class="form-label">اسم المادة</label>
            <input type="text" class="form-control" id="material_name" name="material_name" required>
        </div>
        <div class="col-md-4">
            <label for="class_id" class="form-label">الصف</label>
            <select class="form-select" id="class_id" name="class_id" required>
                <option value="">اختر الصف</option>
                <?php
                $q = $conn->query("SELECT id, name FROM classes ORDER BY id");
                while($row = $q->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2 align-self-end d-flex gap-1">
            <button type="submit" class="btn btn-success w-100" id="save-btn">إضافة</button>
            <button type="button" class="btn btn-secondary w-100 d-none" id="cancel-btn">إلغاء</button>
        </div>
    </form>

    <div id="msg"></div>
    <h5>قائمة المواد</h5>
    <div id="materials-table"></div>
</div>

<script>
function fetchMaterials(classId = "") {
    $("#debug").text("fetchMaterials: classId=" + classId);
    $.get('material_actions.php', {action: 'fetch', class_id: classId}, function(data){
        $("#materials-table").html(data);
        $("#debug").text($("#debug").text() + " | تم جلب المواد");
    }).fail(function(xhr){
        $("#debug").text("fetchMaterials failed: " + xhr.status + " - " + xhr.statusText);
    });
}

function resetForm() {
    $("#material_id").val('');
    $("#material_name").val('');
    $("#save-btn").text("إضافة");
    $("#cancel-btn").addClass("d-none");
    $("#debug").text("resetForm: النموذج أعيد لوضع الإضافة");
}

$(document).ready(function(){

    fetchMaterials($("#class_id").val());

    // فلترة حسب الصف
    $("#class_id").change(function(){
        let classId = $(this).val();
        fetchMaterials(classId);
        resetForm();
    });

    // إضافة أو تعديل
    $("#material-form").submit(function(e){
        e.preventDefault();
        let formData = $(this).serialize() + '&action=save';
        $("#debug").text("submit: data=" + formData);
        $.post('material_actions.php', formData, function(resp){
            $("#msg").html(resp).fadeIn().delay(1000).fadeOut();
            let classId = $("#class_id").val();
            fetchMaterials(classId);
            resetForm();
        }).fail(function(xhr){
            $("#debug").text("submit failed: " + xhr.status + " - " + xhr.statusText);
        });
    });

    // تعبئة النموذج للتعديل
    $(document).on('click', '.edit-btn', function(){
        let id = $(this).data('id');
        $("#debug").text("edit-btn clicked: id=" + id);
        $.get('material_actions.php', {action: 'get', id: id}, function(data){
            // لاحظ: لا تستخدم JSON.parse
            $("#material_id").val(data.id);
            $("#material_name").val(data.name);
            $("#class_id").val(data.class_id);
            $("#save-btn").text("تعديل");
            $("#cancel-btn").removeClass("d-none");
            $("#debug").text($("#debug").text() + " | النموذج جاهز للتعديل");
        }).fail(function(xhr){
            $("#debug").text("AJAX get failed: " + xhr.status + " - " + xhr.statusText);
        });
    });

    // زر إلغاء التعديل
    $("#cancel-btn").click(function(){
        resetForm();
    });

    // حذف
    $(document).on('click', '.delete-btn', function(){
        if(!confirm("هل أنت متأكد من الحذف؟")) return;
        let id = $(this).data('id');
        let classId = $("#class_id").val();
        $("#debug").text("delete-btn clicked: id=" + id);
        $.post('material_actions.php', {action: 'delete', id: id}, function(resp){
            $("#msg").html(resp).fadeIn().delay(1000).fadeOut();
            fetchMaterials(classId);
            resetForm();
        }).fail(function(xhr){
            $("#debug").text("delete failed: " + xhr.status + " - " + xhr.statusText);
        });
    });

});
</script>
</body>
</html>