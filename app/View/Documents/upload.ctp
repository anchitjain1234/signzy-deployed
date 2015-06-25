<?php $this->assign('title', 'Upload Documents'); ?>

<div class="container" id="drop">
    <div id="alertdiv">
        
    </div>
    <div class="row">
        <div class="col-md-6" style="height:500px;  " id="upload_preview">
            <!-- <img src="img/sample_nda.png" /> -->

            <p id="upload_progress">Your uploaded document will appear here</p>

        </div>
        <div class="col-md-6" >
              <!-- <img src="img/sample_nda.png" /> -->


            <?php echo $this->Form->create(); ?>
            <div class="input text form-group has-feedback">
                <?php
                echo $this->Form->label('name', 'Document Name:', array('class' => 'control-label'));
                echo $this->Form->input("name", array('class' => 'form-control', 'placeholder' => 'Name of document', 'id' => 'docname', 'label' => false, 'autocomplete' => "off"));
                ?>
            </div>
            <div class="input text form-group has-feedback" id="droplink">
                <?php
                echo $this->Form->label('file', 'Document(You can also drop file here):', array('class' => 'control-label'));
                echo $this->Form->input("file", array('type' => 'file', 'class' => 'form-control', 'id' => 'doc', 'label' => false, 'autocomplete' => "off", "required" => "required"));
                ?>
            </div>
            <ul id="document_name_with_progress">

            </ul>
            <?php
            echo $this->Form->input("emails_hidden", array("type" => "hidden", "id" => "emails_hidden"));
            ?>
            <b>Signatories</b>:
            <ul class="list-group" id="signatory_holder"></ul>
            <div class="submit form-group has-feedback">
                <button id="<?php echo $useremail ?>" type="button" class="btn btn-primary signatory_button" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus"></span>Add Signatory</button>
            </div>
            <?php
            echo $this->Form->button('Save', ['type' => 'submit', 'class' => 'btn btn-success btn-block', 'div' => false, 'id' => "senddoc"]);
            echo $this->Form->end();
            ?>
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
                            <?php echo $this->Form->label('usernsme', 'Email:', array('class' => 'control-label')); ?>
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

<?php
echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('jquery-ui.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('jquery.knob.js');
echo $this->Html->script('jquery.iframe-transport.js');
echo $this->Html->script('jquery.fileupload.js');
echo $this->Html->script('upload.js');
?>
