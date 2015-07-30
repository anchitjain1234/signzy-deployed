function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

$(function () {

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

    $.sign_document = function () {
        $.ajax({
            url: "sign",
            method: "POST",
            data: {"status": 1, "userid": userid, "docuid": docuid}
        });
        window.location = "trail/" + docuid;
    };

    $.reject_document = function () {
        $.ajax({
            url: "sign",
            method: "POST",
            data: {"status": 3, "userid": userid, "docuid": docuid}
        });
        window.location = "trail/" + docuid;
    };

    $.void_document = function () {
        $.ajax({
            url: "sign",
            method: "POST",
            data: {"status": 2, "userid": userid, "docuid": docuid}
        });
        window.location = "trail/" + docuid;
    };

    $('#sure_success').click($.sign_document);
    $('#decline_sign').click($.reject_document);
    $('#void_sign').click($.void_document);
});
