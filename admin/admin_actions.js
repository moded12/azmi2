$(document).ready(function(){

  // مشاهدة موضوع
  $(document).on('click', '.view-item', function(){
    var threadId = $(this).data('id');
    $.get('manage_content_ajax.php', {action: 'view_thread', id: threadId}, function(html){
      $('#mainContentPage').html(html);
    });
  });

  // تعديل موضوع
  $(document).on('click', '.edit-item', function(){
    var threadId = $(this).data('id');
    $.get('manage_content_ajax.php', {action: 'edit_thread_form', id: threadId}, function(html){
      $('#mainContentPage').html(html);
    });
  });

  // حذف موضوع
  $(document).on('click', '.delete-item', function(){
    var threadId = $(this).data('id');
    if(confirm('هل أنت متأكد أنك تريد حذف هذا الموضوع وجميع مرفقاته نهائياً؟')) {
      $.post('manage_content_ajax.php', {action: 'delete_thread', id: threadId}, function(msg){
        alert(msg);
        $('.dynamic-search-box select').trigger('change');
      });
    }
  });

});