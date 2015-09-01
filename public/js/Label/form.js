$(function() {
	$("#label_form").on("click", '#submit', function(e){
        e.preventDefault();
        var action = $("#label_form").attr('action');
        
        var submit       = 1;
        var id           = $('#label_id').val();
        var name         = $('#name').val();
        var default_text = $('#default_text').val();
    	
        $.ajax({
            dataType: "html",
            url: action,
            type: "POST",
            data: { submit: submit, id: id, name: name, default_text: default_text },
            beforeSubmit: function() {
            },
            success: function(data) {
                $('#form').html(data);
                $.getScript( "/js/Label/form.js");
                $("#labels_table").dataTable().fnReloadAjax();
                $('html,body').animate({ scrollTop: $('#form').offset().top }, { duration: 'slow', easing: 'swing'});
            }
      });
    	return false;
    });
});