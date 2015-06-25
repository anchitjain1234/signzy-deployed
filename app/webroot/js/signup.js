var fl = 0;
var score = 0;
var nameerror = 0;
var emailerror = 0;
var pwderror = 0;
var pwdreentererror = 0;
//Copied from stackoverflow.
function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
	return pattern.test(emailAddress);
};

jQuery(document).ready(function() {

	"use strict";
	var options = {};
	options.common = {
		minChar: 8,
		usernameField: "#email",
		onKeyUp: function(evt, data) {
			$("#length-help-text").text("Current length: " + $(evt.target).val().length + " and score: " + data.score);
			score = data.score;
		},
		debug: false
	};
	options.ui = {
		showPopover: false,
		showErrors: false,
		useVerdictCssClass: true,
		showVerdictsInsideProgressBar: true,
		showProgressBar: true,
		verdicts: [
			"<span class='glyphicon glyphicon-remove'></span> Weak",
			"<span class='glyphicon glyphicon-exclamation-sign'></span> Normal",
			"Medium",
			"<span class='glyphicon glyphicon-thumbs-up'></span> Strong",
			"<span class='glyphicon glyphicon-ok'></span> Very Strong"
		],
		spanError: function(options, key) {
			var text = options.ui.errorMessages[key];
			return '<span style="color: #d52929">' + text + '</span>';
		},
		popoverError: function(errors) {
			var message = "<div><ul class='error-list' style='margin-bottom: 0;'>";
			jQuery.each(errors, function(idx, err) {
				if (err !== '<span style="color: #d52929">undefined</span>') {
					message += "<li>" + err + "</li>";
				}

			});
			message += "</ul></div>";
			return message;
		}
	};
	options.rules = {
		activated: {
			wordNotEmail: true,
			wordLength: true,
			wordSimilarToUsername: true,
			wordSequences: true,
			wordTwoCharacterClasses: true,
			wordRepetitions: true,
			wordOneSpecialChar: true,
			wordLetterNumberCombo: true

		}
	};
	$('#password').pwstrength(options);

	$.pwd_confirmation = function() {

		var pwd = $("#password").val();
		var pwd_reenter = $("#renter_password").val();

		if (pwd !== pwd_reenter || pwd === "") {
			pwdreentererror = 1;
			$("#pwd_repeat_glyph").attr('class', 'glyphicon glyphicon-remove form-control-feedback');
			$('#renter_password').tooltip({
				title: "<div>Password does not matches.</div>",
				animation: true,
				placement: 'right',
				html: true,
				trigger: "hover focus"
			});
		} else {
			pwdreentererror = 0;
			$("#pwd_repeat_glyph").attr('class', 'glyphicon glyphicon-ok form-control-feedback');
			$('#renter_password').tooltip("destroy");
		};

	};

	$.pwd_check = function() {

			var pwd = $("#password").val();
			if (score < 17 || pwd === "") {
				pwderror = 1;
				$("#pwd_glyph").attr('class', 'glyphicon glyphicon-remove form-control-feedback');
				$('#password').tooltip({
					title: "<div>Please enter more secure password</div>",
					animation: true,
					placement: 'right',
					html: true,
					trigger: "hover focus"
				});
			} else {
				pwderror = 0;
				$("#pwd_glyph").attr('class', 'glyphicon glyphicon-ok form-control-feedback');
				$('#password').tooltip("destroy");
			};
		};
		// Copied from stackoverflow.
	$.email_check = function() {
		var email_entered = $("#useremail").val();
		if (!isValidEmailAddress(email_entered)) {
			emailerror = 1;
			$("#email_glyph").attr('class', 'glyphicon glyphicon-remove form-control-feedback');
			$('#useremail').tooltip({
				title: "<div>Please enter valid email address</div>",
				animation: true,
				placement: 'right',
				html: true,
				trigger: "hover focus"
			});
		} else {
			emailerror = 0;
			$("#email_glyph").attr('class', 'glyphicon glyphicon-ok form-control-feedback');
			$('#useremail').tooltip("destroy");
		};
	};

	$.name_check = function() {
		var name_entered = $("#userusername").val();
		if (name_entered == "") {
			nameerror = 1;
			$("#name-glyph").attr('class', 'glyphicon glyphicon-remove form-control-feedback');
			//$('#userusername').tooltip({title: '<div>Please enter your name</div>', animation: true , placement: 'right' , html : true , trigger: "hover focus"});
		} else {
			nameerror = 0;
			$("#name-glyph").attr('class', 'glyphicon glyphicon-ok form-control-feedback');
			//$('#userusername').tooltip("destroy");
		}
	};

	$.disable_submit = function() {
		if (pwdreentererror === 1 || pwderror === 1 || emailerror === 1 || nameerror === 1) {
			$("#submitbutton").attr('disabled', 'disabled');
		} else {
			$("#submitbutton").removeAttr('disabled');
		};
	};



	$("#renter_password").on('keyup focus hover', $.pwd_confirmation);
	$("#renter_password").on('keyup focus hover', $.disable_submit);
	//$("#renter_password").focus($.pwd_confirmation,$.disable_submit);
	//$("#renter_password").hover($.pwd_confirmation,$.disable_submit);
	//$("#renter_password").focus($.pwd_confirmation,$.disable_submit);
	//$("#password").keyup($.pwd_confirmation , $.pwd_check ,$.disable_submit);
	$("#password").on('keyup focus hover', $.pwd_check);
	$("#password").on('keyup focus hover', $.pwd_confirmation);
	$("#password").on('keyup focus hover', $.disable_submit);
	//$("#password").hover($.pwd_confirmation , $.pwd_check ,$.disable_submit);
	//$("#password").focus($.pwd_confirmation , $.pwd_check ,$.disable_submit);
	//$("#useremail").focus($.email_check ,$.disable_submit);
	$("#useremail").on('keyup focus hover change', $.email_check);
	$("#useremail").on('keyup focus hover change', $.disable_submit);
	//$("#useremail").hover($.email_check ,$.disable_submit);
	//$("#useremail").focus($.email_check ,$.disable_submit);
	//$("#useremail").change($.email_check ,$.disable_submit);
	//$("#userusername").on('keyup focus hover change',$.disable_submit);
	$("#userusername").on('keyup focus hover change', $.name_check);
	$("#userusername").on('keyup focus hover change', $.disable_submit);


	// $("#userusername").focus($.name_check , $.disable_submit);
	// $("#userusername").hover($.name_check,$.disable_submit);
	// $("#userusername").focus($.name_check,$.disable_submit);
	// $("#userusername").select($.name_check,$.disable_submit);
	// $("#userusername").blur($.name_check,$.disable_submit);
	// $("#userusername").change($.name_check,$.disable_submit);
});

$(function() {
	$('[data-toggle="popover"]').popover();
});
