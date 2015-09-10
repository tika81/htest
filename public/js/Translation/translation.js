$(function() {
    $("#labels_table tbody .add_translation").each(function(){
        $(this).on("click", function(e) {
            e.preventDefault();
            
            var label_id = $(this).parent().attr('label_id');
            var lang = $(this).attr('lang');
            var data = { label_id: label_id, language: lang };
            
            translationForm('add', data);
            
            return false;
        });
    });
    $("#labels_table tbody .edit_translation").each(function(){
        $(this).on("click", function(e) {
            e.preventDefault();
            
            var trans_id = $(this).attr('trans_id');
            var data = { id: trans_id };
            
            translationForm('edit', data);
            
            return false;
        });
    });
    
    $("#labels_table tbody .delete_translation").each(function(){
        $(this).on("click", function(e) {
            e.preventDefault();
            
            var trans_id = $(this).attr('trans_id');
            var question = 'Do you really want to delete this translation?'
            if (confirm(question)) {
                //ajax delete
                $.ajax({
                    dataType: "html",
                    url: "translation/delete",
                    data: { trans_id: trans_id },
                    type: "post",
                    success: function(data) {
                        $('#form').html(data);
                        $('html,body').animate({ scrollTop: $('#form').offset().top }, { duration: 'slow', easing: 'swing'});
                        $("#labels_table").dataTable().fnReloadAjax();
                        
                        return false;
                    }
                 });
            }
        });
    });
    
    function translationForm(action, data){
        $.ajax({
            dataType: "html",
            url: 'translation/' + action,
            type: "post",
            data: data,
            success: function(data) {
                $.getScript( "js/Translation/form.js");
                $('#form').html(data);
                $('html,body').animate({ scrollTop: $('#form').offset().top }, { duration: 'slow', easing: 'swing'});
            }
        });
    }
    
});