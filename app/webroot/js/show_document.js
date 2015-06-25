$(function() {
	$("#delete_delete").attr('disabled', 'disabled');
	$("#delete").click(function(e) {
		$("#modal_delete").modal();
	});

	$.check_doc_name = function() {
		var entered_name = $('#delete_docname_area').val();

		if (entered_name === $('.delete_donot').attr('id')) {
			$("#delete_delete").removeAttr('disabled');
		} else {
			$("#delete_delete").attr('disabled', 'disabled');
		}
	};

	$.send_delete_request = function() {
		$.ajax({
			url: "../delete.json",
			method: "POST",
			data: {
				"docuid": $('.docidholder').attr('id')
			},
			success: function(text) {
				//alert(JSON.parse(text));
				//console.log(JSON.parse(text));
				if(text['status'])
				{
					window.location = "../../dashboard";
				}
				else
				{
					alert('Error while deleting.Please try again later');
				}
			},
			error: function(text) {
				alert('Error while deleting.Please try again later');
			}
		});
	};



	$("#delete_docname_area").on('keyup focus hover keydown', $.check_doc_name);
  $('#delete_delete').click($.send_delete_request);

});
