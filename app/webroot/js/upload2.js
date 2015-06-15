(function($) {
  var name_selected;
  var email_entered;
  var emails =[];
$('#email_search').autocomplete({
      source: "http://localhost/cakephp/col/index.json",
      open: function() {
       $(this).autocomplete("widget")
              .appendTo("#results")
              .css("position", "static");
   },
   response: function(event, ui) {
            // ui.content is the array that's about to be sent to the response callback.
            if (ui.content.length === 0) {
                $("#empty-message").text("No results found");
                $("#add_button").attr('disabled','disabled');
            } else {
                $("#empty-message").empty();
            }
        },
  select: function( event, ui ) {
    name_selected = ui.item.value;
    $("#add_button").removeAttr('disabled');
    return false;
  }
});

$.email_address_appearing_in_list = function ()
{
  var str1="<li class='list-group-item'>";
  var str2=" (";
  var str3=") <span class='label label-primary'>Biometric required</span><a href='#' class='pull-right delete_signatory '><span class='glyphicon glyphicon-remove'></span></a></li>";
  var template=str1.concat(name_selected,str2,email_entered,str3);
  $("#signatory_holder").append(template);

  $("#signatory_holder li").hover(function(e){
           $(e.target).find(".delete_signatory").show();
         },function(e){
           $(e.target).find(".delete_signatory").hide();
         });

         $("#signatory_holder .delete_signatory").hide();
         $("#signatory_holder .delete_signatory").off("click");
         $("#signatory_holder .delete_signatory").click(function(e){
           // console.log(e);
           $(e.target).parent().parent().remove();
           return false;
         });

         $('#myModal').modal();
         return false;
}

$.email_uniqueness_check = function()
{
  console.log(emails);
  email_entered  = $('#email_search').val();
  console.log((jQuery.inArray(email_entered,emails)));
  if(jQuery.inArray(email_entered,emails) == -1)
  {
    emails.push(email_entered);
    var str1="<li class='list-group-item'>";
    var str2=" (";
    var str3=") <span class='label label-primary'>Biometric required</span><a href='#' class='pull-right delete_signatory '><span class='glyphicon glyphicon-remove'></span></a></li>";
    var template=str1.concat(name_selected,str2,email_entered,str3);
    console.log(email_entered);
    $("#signatory_holder").append(template);

    $("#signatory_holder li").hover(function(e){
             $(e.target).find(".delete_signatory").show();
           },function(e){
             $(e.target).find(".delete_signatory").hide();
           });
           /*
           We have to also delete the selected email from the emails array as well.
           Write code here to take care of that.
           */
           $("#signatory_holder .delete_signatory").hide();
           $("#signatory_holder .delete_signatory").off("click");
           $("#signatory_holder .delete_signatory").click(function(e){
             //console.log(e);
             $(e.target).parent().parent().remove();

             return false;
           });

           $('#myModal').modal();
           return false;
  }
  else
  {
    alert('Email address already entered.');
  }
}

$('#add_button').click($.email_uniqueness_check);
})(jQuery);
