$(function() {
	$("#translation_form").on("click", '#submit', function(e){
        e.preventDefault();
        var action = $("#translation_form").attr('action');
        
        var submit   = 1;
        var id       = $('#trans_id').val();
        var label_id = $('#label_id').val();
        var language = $('#language').val();
        var text     = $('#text').val();
        
        $.ajax({
            dataType: "html",
            url: action,
            type: "POST",
            data: { submit: submit, id: id, label_id: label_id, language: language, text: text },
            beforeSubmit: function() {
            },
            success: function(data) {
                $('#form').html(data);
                $.getScript( "/js/Translation/form.js");
                $("#labels_table").dataTable().fnReloadAjax();
                $('html,body').animate({ scrollTop: $('#form').offset().top }, { duration: 'slow', easing: 'swing'});
            }
      });
        return false;
    });
});