<?php $this->assign('title', 'Signup'); ?>
<?php echo $this->Html->css('signup', null, array('inline' => false)); ?>
<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-primary">
                <div class="panel-heading">Sign Up</div>
                <div class="panel-body">

                    <?php echo $this->Form->create('User'); ?>
                    <div class="form-group">
                        <div class="form-group has-feedback">
                            <?php
                            echo $this->Form->label('name', 'Full Name:', array('class' => 'control-label'));
                            echo $this->Form->input("name", array('class' => 'form-control', 'placeholder' => 'Name, as per your legal documents', 'id' => 'userusername', 'label' => false, 'autocomplete' => "off"));
                            ?>
                            <i id="name-glyph" class="glyphicon glyphicon-user form-control-feedback"></i>
                        </div>
                    </div>

                    <div class="form-group" id="company_name_div" style="display:none">
                        <div class="form-group has-feedback">
                            <?php
                            echo $this->Form->label('companyname', 'Name of Company:', array('class' => 'control-label'));
                            echo $this->Form->input("companyname", array('class' => 'form-control', 'placeholder' => 'Name of your company',
                                                                          'id' => 'usercompanyname', 'label' => false, 'autocomplete' => "off" ,
                                                                          'required' => 'required',"type"=>'hidden'));
                            ?>
                            <div class="error-message"> <?php if (isset($companymatcherror) && $companymatcherror) echo "Please make sure company name is same as email domain."; ?></div>
                            <i id="name-glyph" class="glyphicon glyphicon-user form-control-feedback"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-group has-feedback ">
                            <?php
                            echo $this->Form->label('username', 'Email:', array('class' => 'control-label','id'=>'useremail_label'));
                            echo $this->Form->input("username", array('class' => 'form-control', 'placeholder' => 'Enter email', 'id' => 'useremail', 'label' => false, 'autocomplete' => "off"));
                            ?>
                            <i id ="email_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-group has-feedback ">
                            <?php
                            echo $this->Form->label('password', 'Password:', array('class' => 'control-label'));
                            echo $this->Form->input("password", array('class' => 'form-control', 'placeholder' => 'Enter password', 'label' => false, 'id' => 'password'));
                            ?>

                            <i id="pwd_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
                        </div>
                    </div>
                    
                    <div class="form-group has-feedback ">
                        <label for="password_repeat" class="control-label">Confirm Password:</label>
                        <input type="password" class="form-control" id="renter_password" placeholder="Enter password again" id="renter_password" name="renter_password" required="required">
                        <div class="error-message"> <?php if ($pwdmatcherror) echo "Password does not matches."; ?></div>
                        <i id="pwd_repeat_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
                    </div>

                    <div class="form-group">
                        <div class="form-group has-success has-feedback">

                        </div>
                    </div>
                    <?php
                    echo $this->Form->button('Reset', array('type' => 'reset', 'class' => "btn btn-danger"));
                    echo $this->Form->button('Sign Up', array('type' => 'submit', 'class' => 'btn btn-primary', 'div' => false, 'id' => "submitbutton","disabled"=>"disabled"));
                    echo $this->Form->button('Signup as Company', array('type' => 'button', 'class' => "btn btn-default pull-right", 'id' => 'signup_as_company_btn'));
                    echo $this->Form->button('Signup as Individual', array('type' => 'button', 'class' => "btn btn-default pull-right", 'id' => 'signup_as_individual_btn','style'=>"display:none"));
                    echo $this->Form->end();
                    ?>
                </div>
            </div>
            <?php echo $this->Html->link('Sign In', array('action' => 'login')); ?>
        </div>
    </div>

    <hr>

</div> <!-- /container -->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<?php
echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('pwstrength.js');
echo $this->Html->script('signup.js');
?>
<!-- Include all compiled plugins (below), or include individual files as needed -->
