jQuery(document).ready(function () {
    var checked_boxes;
    var ids_checked;

    $('.checkbox').change(function () {

        ids_checked = [];
        checked_boxes = $('input[name="users_checkbox"]:checked');
        checked_boxes.each(function () {
            ids_checked.push($(this).attr('id'));
        });

        if (checked_boxes.length > 0)
        {
            $('#authorize_user_btn').removeAttr('disabled');
            $('#reject_user_btn').removeAttr('disabled');
        }
        else
        {
            $('#authorize_user_btn').attr('disabled', 'disabled');
            $('#reject_user_btn').attr('disabled', 'disabled');
        }

    });

    $('#authorize_user_btn').click(function () {
        $('#authorize_user_btn').html('Authorizing ...');
        $('#authorize_user_btn').attr('disabled', 'disabled');
        $('#reject_user_btn').attr('disabled', 'disabled');
        $.ajax({
            url: '../authorize.json',
            method: "POST",
            data: {"ids": JSON.stringify(ids_checked), "cid": $('.table-hover').attr('id')}
        }).success(function (res) {
            if (res['error'] === '')
            {
                $('#alertdiv').append("<div id=\"alert\"></div>");
                $('#alert').addClass("alert alert-danger");
                $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong>Unknown error.");
                $('#authorize_user_btn').html('Authorize');
                $('#authorize_user_btn').removeAttr('disabled');
                $('#reject_user_btn').removeAttr('disabled');
            }
            else
            {
                location.reload();
            }
        }).fail(function (res) {
            $('#alertdiv').append("<div id=\"alert\"></div>");
            $('#alert').addClass("alert alert-danger");
            $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong>Network error.Please check your intenet connection.");
            $('#authorize_user_btn').html('Authorize');
            $('#authorize_user_btn').removeAttr('disabled');
            $('#reject_user_btn').removeAttr('disabled');
        });
    });

    $('#reject_user_btn').click(function () {
        $('#reject_user_btn').html('Rejecting ...');
        $('#reject_user_btn').attr('disabled', 'disabled');
        $('#authorize_user_btn').attr('disabled', 'disabled');
        $.ajax({
            url: '../reject.json',
            method: "POST",
            data: {"ids": JSON.stringify(ids_checked), "cid": $('.table-hover').attr('id')}
        }).success(function (res) {
            if (res['error'] === '')
            {
                $('#alertdiv').append("<div id=\"alert\"></div>");
                $('#alert').addClass("alert alert-danger");
                $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong>Unknown error.");
                $('#reject_user_btn').html('Reject');
                $('#reject_user_btn').removeAttr('disabled');
                $('#authorize_user_btn').removeAttr('disabled');
            }
            else
            {
                location.reload();
            }
        }).error(function (res) {
            $('#alertdiv').append("<div id=\"alert\"></div>");
            $('#alert').addClass("alert alert-danger");
            $('#alert').html("<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n\
                                          <strong>Error!</strong>Network error.Please check your intenet connection.");
            $('#reject_user_btn').html('Reject');
            $('#reject_user_btn').removeAttr('disabled');
            $('#authorize_user_btn').removeAttr('disabled');
        });
    });
});

