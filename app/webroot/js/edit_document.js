(function ($) {
    var newname;
    var name_selected;
    var email_entered;
    var emails = [];
    var current_emails = $('#signatory_holder li a');
    current_emails.each(function (index)
    {
        emails.push($(this).attr('id'));
    });
    var emails_json;


    var useremail = $('.signatory_button').attr('id');
    $('#email_search').autocomplete({
        source: "../../col/index.json",
        open: function () {
            $(this).autocomplete("widget")
                    .appendTo("#results")
                    .css("position", "static");
        },
        response: function (event, ui) {
            // ui.content is the array that's about to be sent to the response callback.
            if (ui.content.length === 0) {
                $("#empty-message").text("No results found");
                $("#add_button").attr('disabled', 'disabled');
            } else {
                $("#empty-message").empty();
            }
        },
        select: function (event, ui) {
            name_selected = ui.item.value;
            $("#add_button").removeAttr('disabled');
        }
    });

    $.email_uniqueness_check = function () {
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

                $("#signatory_holder li").hover(function (e) {
                    $(e.target).find(".delete_signatory").show();
                }, function (e) {
                    $(e.target).find(".delete_signatory").hide();
                });

                $("#signatory_holder .delete_signatory").hide();
                $("#signatory_holder .delete_signatory").off("click");
                $("#signatory_holder .delete_signatory").click(function (e) {
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

    $("#signatory_holder li").hover(function (e) {
        $(e.target).find(".delete_signatory").show();
    }, function (e) {
        $(e.target).find(".delete_signatory").hide();
    });

    $("#signatory_holder .delete_signatory").hide();
    $("#signatory_holder .delete_signatory").off("click");
    $("#signatory_holder .delete_signatory").click(function (e) {
        $(e.target).parent().parent().remove();
        var email_removed = $(this).attr('id');
        var index = emails.indexOf(email_removed);
        if (index > -1) {
            emails.splice(index, 1);

        }
    });

    $('#new_document_name').on('keyup focus hover change', function ()
    {
        newname = $('#new_document_name').val();
    });


    $.submit_document_changes = function () {
        $('#change_document').html('Submitting ...');
        $('#change_document').attr('disabled', 'disabled');
        emails_json = JSON.stringify(emails);
        $.ajax({
            url: '../change_document.json',
            method: "POST",
            data: {"emails": emails_json, "newname": newname, "docuid": $('.docidholder').attr('id')}
        }).success(function (res) {
            if (res['status'])
            {
                location.reload();
            }
            else
            {
                console.log(res);
                console.log('some error');
                //window.location = "../index";
            }
        }).error(function (res) {
            alert('Error while saving data.Please try again later');
        });
    };

    $('#change_document').click($.submit_document_changes);
})(jQuery);
