jQuery(document).ready(function() {
	$('#signinform').validate({
		rules: {
			'data[User][username]': {
				required: true,
				email: true
			},
			'data[User][password]': {
				required: true,
				minlength: 8
			},
			messages: {
				'data[User][username]': {
					required: 'Please enter your email address.',
					email: 'Please enter a valid email address.'
				},
				'data[User][password]': {
					required: 'Please enter your password.',
					minlength: 'Password length is short.'
				}
			}
		}
	});

	$.pwd_check = function() {
		if ($('#signinpassword').valid()) {
			$("#pwd_glyph").attr('class', 'glyphicon glyphicon-ok form-control-feedback');
		} else {
			$("#pwd_glyph").attr('class', 'glyphicon glyphicon-remove form-control-feedback');
			$("#signinbutton").attr('disabled', 'disabled');
		};
	}

	$.email_check = function() {
		if ($('#signinemail').valid()) {
			$("#email_glyph").attr('class', 'glyphicon glyphicon-ok form-control-feedback');
		} else {
			$("#email_glyph").attr('class', 'glyphicon glyphicon-remove form-control-feedback');
			$("#signinbutton").attr('disabled', 'disabled');
		};
	}

	$.signinbutton_check = function() {
		if ($('#signinpassword').valid() && $('#signinemail').valid()) {
			$("#signinbutton").removeAttr('disabled');
		} else {
			$("#signinbutton").attr('disabled', 'disabled');
		}
	}

	$("#signinemail").on('keyup focus hover', $.email_check);
	$("#signinemail").on('keyup focus hover', $.signinbutton_check);
	$("#signinpassword").on('keyup focus hover', $.pwd_check);
	$("#signinpassword").on('keyup focus hover', $.signinbutton_check);
	$("#signinbutton").on('focus', $.signinbutton_check);
});
