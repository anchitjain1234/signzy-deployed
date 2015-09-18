<?php $this->assign('title', 'Upload Documents'); ?>

<div class="container">
  <div class="row">
    <div class="col-md-6" style="height:500px;overflow-y:scroll;" id="upload_preview">
        <!-- <img src="img/sample_nda.png" /> -->
        <?php
          if (isset($document)) {
              $link = $this->Upload->uploadUrl($document, 'Document.avatar', array('urlize' => 'true'));
              echo "<embed src='".$link."' width = '540' height = '490'></embed>";
          } else {
              echo '<div class="container" >
                    <div class="row align-center">
                      Your uploaded document will appear here.
                    </div>
                  </div>';
          }
        ?>

    </div>
    <div class="col-md-6">
          <!-- <img src="img/sample_nda.png" /> -->
      <form id="DocumentUpload2Form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
        <div style="display:none;"><input type="hidden" name="_method" value="POST">
        </div>
        <div class="input text form-group has-feedback">
          <label for="DocumentName" class="control-label">Document Name:</label>
          <input class="form-control" name="data[Document][name]" maxlength="2000" type="text" id="DocumentName" placeholder="Name of Document">
        </div>
        <div class="input text form-group has-feedback">
          <label for="DocumentAvatar" class="control-label">Document:</label>
          <input class="form-control" type="file" name="data[Document][avatar]" id="DocumentAvatar" required="required">
        </div>
        <input id="emails_hidden" name="emails_hidden" type="hidden" />
        <b>Signatories</b>:
        <ul class="list-group" id="signatory_holder"></ul>
        <div class="submit form-group has-feedback">
          <button id="<?php echo $useremail ?>" type="button" class="btn btn-primary signatory_button" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus"></span>Add Signatory</button>
        </div>
        <input type="submit" value="Upload" class="btn btn-success btn-block" id="submitform">
      </form>
    </div>

  <!-- Modal -->
 <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">Add a signatory</h4>
  </div>
  <div class="modal-body">

            <div class="form-group">
              <?php
              echo $this->Form->label('usernsme', 'Email:', array('class' => 'control-label')); ?>
                <?php echo $this->Form->input('username', array('class' => 'form-control', 'placeholder' => 'Email address', 'id' => 'email_search', 'label' => false, 'autocomplete' => 'off')); ?>
                <div id="results"></div>
                <div id="empty-message"></div>
            </div>
            <!--
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
          -->
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="add_button">Add</button>
  </div>
</div>
</div>
</div>
  </div>
</div>

<?php echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('jquery-ui.min.js');
echo $this->Html->script('tagmanager.js');
echo $this->Html->script('upload2.js');?>
