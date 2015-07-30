$(function () {
    var uid = $('.useridh1').attr('id');
    var pid= $('.pid').attr('id');

    function generate_ajax(option,uid,pid)
    {
        $.ajax({
            url: "verifyuserprofile.json",
            method: "POST",
            data: {"uid": uid,"option":option,"pid":pid}
        }).success(function(res){
            res = JSON.parse(res);
            if(res["success"])
            {
                if(res["success"]==1)
                {
                    alert("User verified successfully.");
                }
                else if(res["success"]==2)
                {
                    alert("User verification rejected successfully.");
                }
            }
            else
            {
                if(res["error"]==1)
                {
                    alert("Data cant be saved .Try later.");
                }
                else
                {
                    alert("Unknown error");
                }
            }
        }).fail(function(res){
            alert("Check your network connection");
        });
    }
    
    $('#verify_user').click(function(){
        generate_ajax("TRUE",uid,pid);
    });
    
    $('#reject_user').click(function(){
        generate_ajax("FALSE",uid,pid);
    })


});


