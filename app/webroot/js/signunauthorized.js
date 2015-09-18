$(function () {
    var cid = $('.compid').attr('id');
    var uid = $('.userid').attr('id');

    $.remind_heads = function () {
        $.ajax({
            url: '../compmember/remind_leagal_heads.json',
            method: 'POST',
            data: {"cid": cid, "uid": uid}
        }).success(function (res) {
            res = JSON.parse(res);
            $('#alertdiv').append("<div id=\"alert\"></div>");
            if(res['success'])
            {
                $('#alert').addClass("alert alert-success");
            $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Success!</strong> Mails sent to legal heads successfully.");
            }
            else
            {
                $('#alert').addClass("alert alert-danger");
                if(res['error'] === 1)
                {
                    $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Company has no legal heads or maybe they have not verified yet.Please try again later.");
                }
                else
                {
                    $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> UNkwon error.Please try again later.");
                }
            }
        }).error(function (res) {
            $('#alertdiv').append("<div id=\"alert\"></div>");
            $('#alert').addClass("alert alert-danger");
            $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong> Network error.Please check if you internet connection is stable");
        });
    };
    
    $('#remind_heads').click(function(){
        $.remind_heads();
    });
});


