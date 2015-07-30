$(function () {
   var uid = $('#uid').attr('class');
   var did= $('#did').attr('class'); 
   
   function generate_ajax(option,uid,did)
    {
        $.ajax({
            url: "photoverification.json",
            method: "POST",
            data: {"uid": uid,"option":option,"did":did}
        }).success(function(res){
//            res = JSON.parse(res);
//            console.log(res["success"]);
            if(res["success"])
            {
                if(res["success"]==1)
                {
                    alert("Photo verified successfully.");
                }
                else if(res["success"]==2)
                {
                    alert("Photo rejected successfully.");
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
    
    $('#photos_match').click(function(){
        generate_ajax("TRUE",uid,did); 
    });
    
    $('#photos_donotmatch').click(function(){
        generate_ajax("FALSE",uid,did); 
    });
});

