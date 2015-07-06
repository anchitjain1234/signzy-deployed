/*
 *
 * Change JS alerts to Bootstrap alerts.
 *
 */
$(function () {
    var ul = $('#document_name_with_progress');
    var upload_preview = $('#upload_preview');
    var name_selected;
    var email_entered;
    var emails = [];
    var emails_json;
    var useremail = $('.signatory_button').attr('id');
    var doc_type;
    var doc_org_name;
    var doc_size;
    var doc_name;
    var file_pdf;
    var companies_selected = [];
    var current_comapny_selected = "" ;
    var biometric_flag = 0;
    var comapny_ino_tobe_sent = {};
    var biometric_info_tobe_sent = [];
    
    $('#company_name_input').attr('type', 'hidden');

    function geterrorcontent(errorcode)
    {
        switch (errorcode)
        {
            case 1:
                return "Filesize exceeds maximum size allowed.";
                break;
            case 2:
                return "Filesize exceeds maximum size allowed.";
                break;
            case 3:
                return "FIle uploaded partially.Please try again.";
                break;
            case 4:
                return "No file uploaded.";
                break;
            case 6:
                return "Error in uploading.Please make sure you have a stable internet connection.";
                break;
        }
    }

    /*
     * Appending emails into list and also checking that no email repeats.
     */
    $.email_uniqueness_check = function () {
        email_entered = $('#email_search').val();

        if (email_entered === useremail) {
            alert("You cant add yourself into signatory.");
        } else {
            if (jQuery.inArray(email_entered, emails) === -1) {
                emails.push(email_entered);
                emails_json = JSON.stringify(emails);
                
                if(current_comapny_selected !== "")
                {
                    comapny_ino_tobe_sent[email_entered] = current_comapny_selected;
                }
                
                if(biometric_flag !== 0)
                {
                    biometric_info_tobe_sent.push(email_entered);
                }
                
                console.log(JSON.stringify(biometric_info_tobe_sent));
                console.log(comapny_ino_tobe_sent);
                var str1 = "<li class='list-group-item'>";
                var str2 = " (";
                var str3;
                var str_temp_company = "";
                var str_temp_biometric = "";
                
                if(current_comapny_selected !== "")
                {
                   str_temp_company = "<span class='label label-info'>"+current_comapny_selected+"</span>";
                }
                
                if(biometric_flag === 1)
                {
                    str_temp_biometric = "<span class=\"label label-primary\">Biometric required</span>";
                }
                
                str3 = ") "+str_temp_company+" "+str_temp_biometric+"<a href='#' class='pull-right delete_signatory' id=";
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
                biometric_flag = 0;
                
            } else {
                alert('Email address already entered.');
            }
        }
    };

    $.opening_company_add = function ()
    {
        $('#company_name_input').attr('type', 'text');
        $('#add_company_btn').hide();
        $('#close_company_add_btn').show();
    };

    $.removing_company_add = function ()
    {
        $('#company_name_input').attr('type', 'hidden');
        $('#close_company_add_btn').hide();
        $('#add_company_btn').show();
    };

    $.closing_modal_and_reset = function ()
    {
        $('#myModal').modal('toggle');
        $('#email_search').val("");
        current_comapny_selected = "";
        $('input[name=company]:checked', '#recent_companies_list').attr('checked', false);
        $('#biometric_checkbox_div :checkbox:checked').attr('checked', false);
    };
    
    $.resest_all_fields = function()
    {
        $('#email_search').val("");
        current_comapny_selected = "";
        $('input[name=company]:checked', '#recent_companies_list').attr('checked', false);
        $('#biometric_checkbox_div :checkbox:checked').attr('checked', false);
    };
    
    $.clearradiobox = function()
    {
        $('input[name=company]', '#recent_companies_list').attr('checked',false);
    };
    
    $('#clearing_radio_box').click(function () {
        $.clearradiobox();
    });
    
    $('#biometric_checkbox_div').on('change',function(){
        if ($('#biometric_checkbox_div :checkbox:checked').length > 0)
        {
            biometric_flag = 1;
        }
        else
        {
            biometric_flag = 0;
        }
    });
    
    // Initialize the jQuery File Upload plugin
    $('#droplink').fileupload({
        url: "upload_ajax",
        // This element will accept file drag/drop uploading
        dropZone: $('#drop'),
        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {

            var ext = data.files[0].name.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'pdf', 'docx']) === -1) {
                $('#alertdiv').append("<div id=\"alert\"></div>");
                $('#alert').addClass("alert alert-danger");
                $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Filetype not allowed.Please upload PDF,DOC or DOCX files only.");

            }
            else if (data.files[0].size > 20000000)
            {
                $('#alertdiv').append("<div id=\"alert\"></div>");
                $('#alert').addClass("alert alert-danger");
                $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Filesize exceeds 20 MB.Please upload file less than 20 MB");
            }
            else
            {
                var tpl = $('<p>Uploading ...</p><br><li class="working"><input type="text" value="0" data-width="100" data-height="100"' +
                        ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /></li>');
                $('#senddoc').html("Uploading...");
                $('#senddoc').attr('disabled', 'disabled');
                // Append the file name and file size

                // Add the HTML to the UL element
                data.context = upload_preview.html(tpl);
                fdata = '<p>' + data.files[0].name + '</p><i>' + formatFileSize(data.files[0].size) + '</i><span></span>\n\
                           <a id="remove_file"><span class="glyphicon glyphicon-remove"></span></a>';

                // Initialize the knob plugin
                tpl.find('input').knob();

                // Listen for clicks on the cancel icon
                tpl.find('span').click(function () {

                    if (tpl.hasClass('working')) {
                        jqXHR.abort();
                    }

                    tpl.fadeOut(function () {
                        tpl.remove();
                    });

                });

                // Automatically upload the file once it is added to the queue
                var jqXHR = data.submit();
            }

        },
        progress: function (e, data) {

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            if (progress === 100) {
                $('#senddoc').html("Save");
                $('#senddoc').removeAttr('disabled');
                data.context.removeClass('working');
                ul.show();
                ul.html(fdata);
                $('#droplink').hide();

                $('#remove_file').click(function () {
                    upload_preview.html("Your uploaded document will be shown here.");

                    $(this).parent().hide();
                    $('#droplink').show();

                });
            }
        },
        fail: function (e, data) {
            // Something has gone wrong!
            $('#alertdiv').append("<div id=\"alert\"></div>");
            $('#alert').addClass("alert alert-danger");
            $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Cannot send data right now.Please try again later.");
        },
        done: function (e, data) {
            var r = data.result;

            r = JSON.parse(r);

            if (r["documentstatus"])
            {

                doc_name = r["documentname"];
                doc_org_name = r["documentoriginalname"];
                doc_size = r["documentsize"];
                doc_type = r["documenttype"];
                file_pdf = r["file_pdf"];
                console.log(file_pdf);
                console.log(window.window.location);
                console.log(doc_name.split('.')[0]);
                upload_preview.html("<iframe src='preview?name=" + doc_name.split('.')[0] + "&type=" + doc_name.split('.')[1] + "&status=temp' width = '540' height = '490'></iframe>");
            }
            else
            {
                $('#alertdiv').append("<div id=\"alert\"></div>");
                $('#alert').addClass("alert alert-danger");
                if (r["error"])
                {
                    $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong>" + geterrorcontent(r["error"]) + ")");
                }
                else
                {
                    $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Unknown error while uploading.Please try again later.");
                }
            }
        }

    });

    /*
     * Searching emails through AJAX.
     */
    $('#email_search').autocomplete({
        source: "../col/index.json",
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
    
    /*
     * Searching companies through AJAX.
     */
    $('#company_name_input').autocomplete({
        source: "../company/company_search.json",
        open: function () {
            $(this).autocomplete("widget")
                    .appendTo("#company_search_results")
                    .css("position", "static");
        },
        response: function (event, ui) {
            // ui.content is the array that's about to be sent to the response callback.
            if (ui.content.length === 0) {
                $("#company-empty-message").text("No results found");
            } else {
                $("#company-empty-message").empty();
            }
        },
        select: function (event, ui) {
            companies_selected.push(ui.item.value);
            $('#recent_companies_list').append("<input type=\"radio\" name=\"company\" value=\"" + ui.item.value + "\">" + ui.item.value + "<br/>");
            $.opening_company_add();
        }
    });

    $('#add_button').click($.email_uniqueness_check);
    $('#add_button').click($.resest_all_fields);

    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });
    
    /*
     * Sending data to server thorugh AJAX.
     */
    $('#senddoc').click(function () {
        if (emails.length > 0)
        {
            $('#emails_hidden').val(emails_json);
            $('#senddoc').html("Saving Data and sending emails...");
            $('#senddoc').attr('disabled', 'disabled');
            $.ajax({
                url: "upload",
                method: "POST",
                data: {"emails": emails_json, "name": $('#docname').val(), "doc_name": doc_name, "doc_org_name": doc_org_name, "doc_size": doc_size,
                    "doc_type": doc_type,"company_info":comapny_ino_tobe_sent
                    ,"biometric_info":JSON.stringify(biometric_info_tobe_sent)}
            }).success(function (res) {
                res = JSON.parse(res);
                console.log('changed.');
                if (res['finaldocstatus'])
                {
                    window.location = "index";
                }
                else
                {
                    $('#alertdiv').append("<div id=\"alert\"></div>");
                    $('#alert').addClass("alert alert-danger");
                    if (res["error"] === 1)
                    {
                        $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Please add atleast one signatory.");
                    }
                    else if (res["error"] === 2)
                    {
                        $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Cannot save data right now.Please try again later.");
                    }
                    else
                    {
                        $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Unknown error.Please try again later.");
                    }
                    $('#senddoc').html("Save");
                    $('#senddoc').removeAttr('disabled');
                    console.log('changed again');
                }
            }).fail(function (res) {
                $('#alertdiv').append("<div id=\"alert\"></div>");
                $('#alert').addClass("alert alert-danger");
                $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Unknown error.Please try again later.");
                $('#senddoc').html("Save");
                $('#senddoc').removeAttr('disabled');
            });

        }
        else
        {
            $('#alertdiv').append("<div id=\"alert\"></div>");
            $('#alert').addClass("alert alert-danger");
            $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Please add atleast one signatory.");

        }
    });

    $('#add_company_btn').click(function () {
        $.opening_company_add();
    });

    $('#close_company_add_btn').click(function () {
        $.removing_company_add();
    });

    $('#recent_companies_list').on('change', function () {
        current_comapny_selected = $('input[name=company]:checked', '#recent_companies_list').val();
        console.log(current_comapny_selected);
    });

    $('#close_modal_btn').click(function () {
        $.closing_modal_and_reset();
    });

    $('#close_modal_btn_upper').click(function () {
        $.closing_modal_and_reset();
    });

    // Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }


});
