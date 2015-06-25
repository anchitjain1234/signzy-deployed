/*
 * 
 * Change JS alerts to Bootstrap alerts.
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
    
    
    function geterrorcontent(errorcode)
    {
        switch(errorcode)
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
            else if(data.files[0].size>20000000)
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
            
            r=JSON.parse(r);
            
            if (r["documentstatus"])
            {
                $('#senddoc').html("Save");
                $('#senddoc').removeAttr('disabled');
                doc_name = r["documentname"];
                doc_org_name = r["documentoriginalname"];
                doc_size = r["documentsize"];
                doc_type = r["documenttype"];
                upload_preview.html("<embed src='../uploads/" + doc_name + "' width = '540' height = '490'></embed>");
            }
            else
            {
                $('#alertdiv').append("<div id=\"alert\"></div>");
                $('#alert').addClass("alert alert-danger");
                if(r["error"])
                {
                    $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong>"+geterrorcontent(r["error"])+")");
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

    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    $('#senddoc').click(function () {
        $('#senddoc').html("Saving Data and sending emails...");
        $('#senddoc').attr('disabled', 'disabled');
        if (emails.length > 0)
        {
            $('#emails_hidden').val(emails_json);
            $.ajax({
                url: "upload",
                method: "POST",
                data: {"emails": emails_json, "name": $('#docname').val(), "doc_name": doc_name, "doc_org_name": doc_org_name, "doc_size": doc_size,
                    "doc_type" : doc_type}
            }).success(function (res) {
                res = JSON.parse(res);
                if(res['finaldocstatus'])
                {
                    window.location = "index";
                }
                else
                {
                    $('#alertdiv').append("<div id=\"alert\"></div>");
                    $('#alert').addClass("alert alert-danger");
                    if(res["error"] === 1)
                    {
                        $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Please add atleast one signatory.");
                    }
                    else if(res["error"] === 2)
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
