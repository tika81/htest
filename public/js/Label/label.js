var table;

$(function() {
    table = $("#labels_table").DataTable({
        "bProcessing": false,
        "bDeferRender": true,
        "aLengthMenu": [20,30,40,100],
        "aoColumns": [
                      { "sType": "string" },
                      { "sType": "string" },
                      { "sType": "string", "bSortable": false },
                      { "sType": "string", "bSortable": false },
                      { "className":'details-control',
                    	  "orderable": false,
                    	  "data": null,
                    	  "defaultContent": ''
                      }
                     ],
        "bPaginate":true,
        "sPaginationType": "full_numbers",
        "iDisplayLength": 20,
        "bJQueryUI": true,
        "bInfo": true,
        "aaSorting":[],
        "bServerSide": true,
        "sAjaxSource": "/label/ajaxGetLabels",
        "fnDrawCallback": function(){
        	$(".edit_label").on("click", function(e) {
                e.preventDefault();
                var id = $(this).attr('vall');
                labelForm('edit', id);
                return false;
            });
        	$(".delete_label").on("click", function(e) {
        		e.preventDefault();
        		var id = $(this).attr('vall');
                var question = 'Do you really want to delete this label?'
                if (confirm(question)) {
                    //ajax delete
                    $.ajax({
                        dataType: "html",
                        url: "/label/delete",
                        data: { 'id': id },
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
        },
                                    
        "fnServerData": function ( sSource, aaData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aaData,
                "success": function(data) {
                    fnCallback(data);
                }
            } );
        }
    });

    $('#labels_table tbody').on('click', 'td.details-control', function(){
    	var tr = $(this).closest('tr');
    	var row = $("#labels_table").DataTable().row( tr );
    	
    	if ( row.child.isShown() ) {
    		// This row is already open - close it
    		row.child.hide();
    		tr.removeClass('shown');
    	}
    	else {
    		var label_id = $(this).prev().children('.delete_label').attr('vall')
            $.ajax({
                dataType: "html",
                url: "/translation/showTranslations",
                data: { label_id: label_id },
                type: "post",
                success: function(data) {
                    $.getScript( "/js/Translation/translation.js");
                	// Open this row
                	row.child( data ).show();
                	tr.addClass('shown');
                    
                    return false;
                }
             });
    	}
    });
    
});