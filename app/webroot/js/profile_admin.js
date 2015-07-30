$(function () {
    
   $('#support').autocomplete({
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
                $("#submit").attr('disabled', 'disabled');
            } else {
                $("#empty-message").empty();
            }
        },
        select: function (event, ui) {
            name_selected = ui.item.value;
            $("#submit").removeAttr('disabled');
        }
    }); 
    
    $("#submit").click(function(){
        $.ajax({
           url:'admin.json',
           method:'POST',
           data:{"email": $('#support').val(),"type":"3"}
        }).success(function(res){
            res = JSON.parse(res);
            if(res['success'])
            {
                alert("Email entered made support succesfully.");
            }
            else
            {
                if(res['error'] === 1)
                {
                    alert("Data cant be saved.Try later");
                }
                else
                {
                    alert("Unknown error.");
                }
            }
        }).fail(function(res){
           alert("Check network connection."); 
        });
    });
});


