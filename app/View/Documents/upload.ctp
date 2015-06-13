<?php $this->assign('title', 'Upload Documents'); ?>

<div class="container">
  <div class="row">
    <div class="col-md-6 bg-highlight text-center" style="height:500px;overflow-y:scroll;" id="upload_preview">
        <!-- <img src="img/sample_nda.png" /> -->
        <div id="upload_overlay">
          <a type="button" class="btn btn-primary btn-lg" style="margin-top:200px;">Upload</a>
        </div>
        <!-- <a href="#" style="margin-top:200px;"><h3>Upload</h3></a> -->
    </div>
    <div class="col-md-6" style="height:500px;">
      <form role="form" method="POST"   >
        <div class="form-group">
          <div class="form-group has-success has-feedback">
            <label for="email">Document Name:</label>
            <input type="username" class="form-control" id="username" placeholder="Enter desired username" aria-describedby="inputSuccess2Status" />
            <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
          </div>
        </div>
        <div class="form-group">
          <div class="form-group has-success has-feedback">
            <label for="email">Signatories:</label><br/>
            <ul class="list-group" id="signatory_holder">

            </ul>
            <button type="submit" id="add_signatory" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>Add Signatory</button>
            <!-- <input type="text" name="tags" id="signatories" placeholder="Enter signatory.." class="tm-input"/> -->
          </div>
        </div>

        <button type="button" class="btn btn-success btn-block">Send</button>
      </form>
    </div>
  </div>

  <hr>
  <footer>
    <p>&copy; VerySure™ 2014</p>
  </footer>
</div> <!-- /container -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">Add a signatory</h4>
  </div>
  <div class="modal-body">
          <form class="form-horizontal">

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Email</label>
              <div class="col-sm-8">
                <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
              </div>
            </div>

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Name</label>
              <div class="col-sm-8">
                <input type="email" class="form-control" id="inputEmail3" placeholder="Name">
              </div>
            </div>

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Authorized Company</label>
              <div class="col-sm-4">
                <input type="radio" name="company"> Company 1 <br/>
                <input type="radio" name="company"> Company 2 <br/>
                <input type="radio" name="company"> Company 3 <br/>

                <br/>
                <button type="button" class="btn btn-default" id="add_company"><span class="glyphicon glyphicon-plus"></span>Add</button>
              </div>
              <div class="col-sm-4"></div>
            </div>

            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
                <div class="checkbox">
                  <label>
                    <input type="checkbox"> Biometric required?
                  </label>
                </div>
              </div>
            </div>

          </form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Add</button>
  </div>
</div>
</div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="js/jquery-1.11.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/tagmanager.js"></script>
<script type="text/javascript">
  $(function () {
    $('[data-toggle="popover"]').popover();

    var template = $("#new_signatory_template").html();
    $("#add_signatory").click(function(e){
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
    });

    $("#upload_preview #upload_overlay").hide();
    $("#upload_preview").hover(function(e){
      $(e.currentTarget).find("#upload_overlay").show();
    },function(e){
      $(e.currentTarget).find("#upload_overlay").hide();
    });
  });

</script>
