$(function () {
    var upload_preview=$('#processed_upload');
    var faceflag;
    var verified_face;
    
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
    
    $('#droplink').fileupload({
        url: "imgupload.json",
        dropZone: $('#drop'),
        add: function (e, data) {
            $('#upload_data').attr('disabled','disabled');
            var ext = data.files[0].name.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['jpg', 'png', 'jpeg']) === -1) {
                $('#alertdiv').append("<div id=\"alert\"></div>");
                $('#alert').addClass("alert alert-danger");
                $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Filetype not allowed.Please upload PDF,DOC or DOCX files only.");

            }
            else
            {
                var tpl = $('<p>Uploading ...</p><br><li class="working list-unstyled"><input type="text" value="0" data-width="100" data-height="100"' +
                        ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /></li>');
                $('#upload_data').html("Uploading...");
                $('#upload_data').attr('disabled', 'disabled');
                // Append the file name and file size

                // Add the HTML to the UL element
                data.context = upload_preview.html(tpl);
                fdata = '<p>' + data.files[0].name + '</p><span></span>\n\
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
                $('#upload_data').html("Submit");
//                $('#upload_data').removeAttr('disabled');
                data.context.removeClass('working');
                console.log(fdata);
                $('#imgname').show();
                $('#imgname').html(fdata);
                $('#droplink').hide();

                $('#remove_file').click(function () {
                    upload_preview.html("Processed upload will be shown here.");

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
                                          <strong>Error!</strong> Cannot send data right now.Please check your internet connection.");
        },
        done: function (e, data) {
            var r = data.result;
            r=JSON.parse(r);
            if (r['documentstatus'])
            {

                doc_name = r["documentname"];
                doc_org_name = r["documentoriginalname"];
                doc_size = r["documentsize"];
                doc_type = r["documenttype"];
                file_pdf = r["file_pdf"];
                console.log(file_pdf);
                console.log(window.window.location);
                console.log(doc_name.split('.')[0]);
                upload_preview.html("<iframe src='preview/"+doc_name+"/'</iframe>");
                
                $.ajax({
                    url: "facefromcard.json",
                    method: "POST",
                    data:{"imgname":doc_name}
                }).success(function (res) {
                    console.log(res);
                    if(res.indexOf("success") > -1)
                    {
                        faceflag = true;
                        processedimg = doc_name;
                        verified_face = processedimg;
                        upload_preview.html("<img src='preview/"+processedimg+"/' alt='face detected'/>");
                        console.log(processedimg);
                        alert("face detected");
                        $('#upload_data').removeAttr('disabled');
                    }
                    else
                    {
                        faceflag = false;
                        alert("Cant detect face.Please upload new image");
                        $('#upload_data').attr('disabled','disabled');
                    }
                }).fail(function (res) {
                    alert("Please check your internet conection.");
                });
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
    
    $('#upload_data').click(function(){
       if(faceflag)
       {
           $.ajax({
               url: "submitverification.json",
               method: "POST",
               data : {"name":verified_face,"cardnumber":$('#cardnumber').val()}
           }).success(function(res){
              res = JSON.parse(res);
              if(res['success'])
              {
                  alert("Verification request sent succesfully.");
              }
              else
              {
                  if(res['error'] === 1)
                  {
                      alert("Data cant be saved.Try again");
                  }
                  else
                  {
                      alert("Unknown error.");
                  }
              }
              
           }).fail(function(res){
               console.log("failure");
           });
       }
    });
    
    
});