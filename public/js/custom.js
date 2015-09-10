$(function() {
    //Highlight selected navigation item
    
    $("#add_label").on("click", function(e){
        e.preventDefault();
        labelForm('add');
        return false;
    });
    
});

function labelForm(action, labelId){
    $.ajax({
        dataType: "html",
        url: 'label/' + action,
        type: "post",
        data: { id: labelId },
        success: function(data) {
            $('#form').html(data);
            $.getScript( "js/Label/form.js");
            $('html,body').animate({ scrollTop: $('#form').offset().top }, { duration: 'slow', easing: 'swing'});
        }
  });
}

