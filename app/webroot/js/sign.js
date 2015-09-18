function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

$(function () {

    var media;
    // The width and height of the captured photo. We will set the
    // width to the value defined here, but the height will be
    // calculated based on the aspect ratio of the input stream.

    var width = 320;    // We will scale the photo width to this
    var height = 0;     // This will be computed based on the input stream

    // |streaming| indicates whether or not we're currently streaming
    // video from the camera. Obviously, we start at false.

    var streaming = false;

    // The various HTML elements we need to configure or control. These
    // will be set by the startup() function.

    var video = null;
    var canvas = null;
    var photo = null;
    var startbutton = null;

    var image_captured_url="";
    
    var userid = getUrlVars()["userid"];
    var docuid = getUrlVars()["docuid"];
    $('[data-toggle="popover"]').popover();

    $(".hover-highlight").hover(function (e) {
        $(e.currentTarget).addClass("bg-info");
    }, function (e) {
        $(e.currentTarget).removeClass("bg-info");
    });

    $("#decline").click(function (e) {
        $('#myModal').modal();
        return;
    });

    $("#accept").click(function (e) {
        $("#modal_accept").modal();
    });

    $("#sign").click(function (e) {
        if ($("#biometric_type").val() === "voicescan") {
            $('#modal_voicescan').modal();
        } else if ($("#biometric_type").val() === "facescan") {
            $('#modal_facescan').modal();
        }
        return false;
    });

    $(".camshot").webcam({
        width: 320,
        height: 240,
        mode: "callback",
        swffile: "/verysure/jquery-webcam/jscam_canvas_only.swf",
        onTick: function () {
        },
        onSave: function () {
        },
        onCapture: function () {
        },
        debug: function () {
        },
        onLoad: function () {
        }
    });
    
    function send_ajax_Request(status)
    {
        $.ajax({
            url: "sign.json",
            method: "POST",
            data: {"status": status, "userid": userid, "docuid": docuid,"image":image_captured_url}
        }).success(function (res) {
//            res = JSON.parse(res);
            if (res['success'])
                {
                    window.location = "trail/" + docuid;
                }
            else
            {
                if(res['error'] === 1)
                {
                    alert("Error while saving data.Try again ");
                }
                else if(res['error'] === 2)
                {
                    alert("No facescan image provided. ");
                }
                else
                {
                    alert("Unknown error.Please report to support. ");
                }
            }
        }).fail(function (res) {
            alert("Please check your internet connection. ");
        });
    }

    $.sign_document = function () {
        send_ajax_Request(1);
    };

    $.reject_document = function () {
        send_ajax_Request(3);
    };

    $.void_document = function () {
        send_ajax_Request(2);
    };

    function startup() {
        video = document.getElementById('video');
        canvas = document.getElementById('canvas');
        photo = document.getElementById('photo');
        startbutton = document.getElementById('startbutton');

        navigator.getMedia = (navigator.getUserMedia ||
                navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia ||
                navigator.msGetUserMedia);

        navigator.getMedia(
                {
                    video: true,
                    audio: false
                },
        function (stream) {
            media = stream;
            if (navigator.mozGetUserMedia) {
                video.mozSrcObject = stream;
            } else {
                var vendorURL = window.URL || window.webkitURL;
                video.src = vendorURL.createObjectURL(stream);
            }
            video.play();
        },
                function (err) {
                    console.log("An error occured! " + err);
                }
        );

        video.addEventListener('canplay', function (ev) {
            if (!streaming) {
                height = video.videoHeight / (video.videoWidth / width);

                // Firefox currently has a bug where the height can't be read from
                // the video, so we will make assumptions if this happens.

                if (isNaN(height)) {
                    height = width / (4 / 3);
                }

                video.setAttribute('width', width);
                video.setAttribute('height', height);
                canvas.setAttribute('width', width);
                canvas.setAttribute('height', height);
                streaming = true;
            }
        }, false);

        startbutton.addEventListener('click', function (ev) {
            takepicture();
            ev.preventDefault();
        }, false);

        clearphoto();
        return media;
    }

    // Fill the photo with an indication that none has been
    // captured.

    function clearphoto() {
        var context = canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, canvas.width, canvas.height);

        var data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
    }

    // Capture a photo by fetching the current contents of the video
    // and drawing it into a canvas, then converting that to a PNG
    // format data URL. By drawing it on an offscreen canvas and then
    // drawing that to the screen, we can change its size and/or apply
    // other changes before drawing it.

    function takepicture() {
        var context = canvas.getContext('2d');
        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            context.drawImage(video, 0, 0, width, height);

            var data = canvas.toDataURL('image/png');
            image_captured_url = data;
            console.log(image_captured_url);
            photo.setAttribute('src', data);
            enablebuttons();

        } else {
            clearphoto();
        }
    }
    
    function enablebuttons() {
        console.log('yay');
        $('#sure_success').removeAttr('disabled');
        $('#decline_sign').removeAttr('disabled');
        $('#void_sign').removeAttr('disabled');
        $('#accept').removeAttr('disabled');
        $('#decline').removeAttr('disabled');
    }
    
    function disablebuttons() {
        console.log('nay');
        $('#sure_success').prop("disabled", true);
        $('#decline_sign').prop("disabled", true);
        $('#void_sign').prop("disabled", true);
        $('#accept').prop("disabled", true);
        $('#decline').prop("disabled", true);
    }
    // Set up our event listener to run the startup process
    // once loading is complete.
//    window.addEventListener('load', startup, false);
    $('#startcamera').click(function () {
        media = startup();
    });
    $('#stopcamera').click(function () {
        console.log(media.getVideoTracks());
        media.stop();
    });
    
    if(image_captured_url === "")
    {
        disablebuttons();
    }
    
    $('#sure_success').click($.sign_document);
    $('#decline_sign').click($.reject_document);
    $('#void_sign').click($.void_document);
    
    $('.close_facescan').click(function(){
        console.log(media.getVideoTracks());
        media.stop();
    });
});
