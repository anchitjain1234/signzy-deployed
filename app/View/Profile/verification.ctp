<?php $this->assign('title', 'Profile Verification'); ?>

<div class="container" id="drop">
    <div id="alertdiv">

    </div>
    <div class="row">
        <h1>Verify your profile by uploading a <b><u>scanned copy</u></b> of any one of the following cards</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h3>Select type of card:</h3>
            <ul class="list-unstyled">
                <li><input type="radio" name="type" value="aadhar">Aadhar Card</li>
                <li><input type="radio" name="type" value="pan" checked>PAN Card</li>
                <li><input type="radio" name="type" value="passport">Passport</li>
                <li><input type="radio" name="type" value="dl" >Driving License</li>
            </ul>

        </div>
        <div class="col-md-6">
            <div>
                <?php echo $this->Form->create(); ?>
                <div class="form-group">
                    <div class="form-group has-feedback ">
                        <?php
                        echo $this->Form->label('cardnumber', 'PAN Card Number:', array('class' => 'control-label', 'id' => 'cardnumber_label'));
                        echo $this->Form->input("cardnumber", array('class' => 'form-control', 'placeholder' => 'Enter PAN Card number', 'id' => 'cardnumber', 'label' => false, 'autocomplete' => "off"));
                        ?>
                    </div>
                    <div class="input text form-group has-feedback" id="droplink">
                        <?php
                        echo $this->Form->label('file', 'Scanned Copy(You can also drop file here):', array('class' => 'control-label'));
                        echo $this->Form->input("file", array('type' => 'file', 'class' => 'form-control', 'id' => 'doc', 'label' => false, 'autocomplete' => "off", "required" => "required"));
                        ?>
                    </div>
                    <ul id="imgname">

                    </ul>
                </div>
                <?php
                echo $this->Form->end();
                ?>
            </div>
            <div style="height:200px;" id="processed_upload">
                <p id="upload_progress">Processed upload will be shown here.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 col-md-offset-5">
            <button id="upload_data" class="btn btn-success" disabled="disabled">Submit</button>
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
echo $this->Html->script('profile_verification.js');
?>