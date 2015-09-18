(function($) {
	var name_selected;
	var email_entered;
	var emails = [];
	var emails_json;
	var useremail = $('.signatory_button').attr('id');
	$('#email_search').autocomplete({
		source: "../col/index.json",
		open: function() {
			$(this).autocomplete("widget")
				.appendTo("#results")
				.css("position", "static");
		},
		response: function(event, ui) {
			// ui.content is the array that's about to be sent to the response callback.
			if (ui.content.length === 0) {
				$("#empty-message").text("No results found");
				$("#add_button").attr('disabled', 'disabled');
			} else {
				$("#empty-message").empty();
			}
		},
		select: function(event, ui) {
			name_selected = ui.item.value;
			$("#add_button").removeAttr('disabled');
		}
	});

	$.email_uniqueness_check = function() {
		email_entered = $('#email_search').val();

		if (email_entered === useremail) {
			alert("You cant add yourself into signatory.");
		} else {
			if (jQuery.inArray(email_entered, emails) === -1) {
				emails.push(email_entered);
				emails_json = JSON.stringify(emails);

				var str1 = "<li class='list-group-item'>";
				var str2 = " (";
				var str3 = ") <span class='label label-primary'>Biometric required</span><a href='#' class='pull-right delete_signatory' id=";
				var str4 = "><span class='glyphicon glyphicon-remove'></span></a></li>";
				var template = str1.concat(name_selected, str2, email_entered, str3, email_entered, str4);
				$("#signatory_holder").append(template);

				$("#signatory_holder li").hover(function(e) {
					$(e.target).find(".delete_signatory").show();
				}, function(e) {
					$(e.target).find(".delete_signatory").hide();
				});

				$("#signatory_holder .delete_signatory").hide();
				$("#signatory_holder .delete_signatory").off("click");
				$("#signatory_holder .delete_signatory").click(function(e) {
					$(e.target).parent().parent().remove();
					var email_removed = $(this).attr('id');
					var index = emails.indexOf(email_removed);
					if (index > -1) {
						emails.splice(index, 1);
					}
				});

				$('#myModal').modal();

			} else {
				alert('Email address already entered.');
			}
		}
	};

	$('#add_button').click($.email_uniqueness_check);
	/*
	$.send_emails = function(){
		// xmlhttp= new XMLHttpRequest();
		// xmlhttp.open("POST","upload2",true);
		// xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		// xmlhttp.send("emails=emails_json");
		// console.log(xmlhttp.responseText);
		// console.log("sent");
		// console.log(xmlhttp);

		$.ajax({
			url : "upload2",
			method : "POST",
			data : {"emails":emails_json}
		}).success(function(res){
			console.log(res);
		}).fail(function(res){
			console.log(res);
		});

		$('#emails_hidden').val(emails_json);
	}
	*/

	$('#submitform').click(function() {
		$('#emails_hidden').val(emails_json);
	});
})(jQuery);
