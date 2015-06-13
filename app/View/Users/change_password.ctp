<?php $this->assign('title', 'Change Password'); ?>

<div class="container">
  <!-- Example row of columns -->
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-primary">
        <div class="panel-heading">Sign Up</div>
        <div class="panel-body">
          <?php echo $this->Form->create('User'); ?>
          <div class="form-group">
            <div class="form-group has-feedback ">
              <?php
              echo $this->Form->label('password', 'Password:', array('class'=>'control-label'));
              echo $this->Form->input("password",array('class' => 'form-control','placeholder' => 'Enter password' ,'label' => false , 'id' => 'password' ));?>

              <i id="pwd_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
            </div>
          </div>
          <div class="form-group has-feedback ">
           <label for="password_repeat" class="control-label">Confirm Password:</label>
              <input type="password" class="form-control" id="renter_password" placeholder="Enter password again" id="renter_password" name="renter_password" required="required">
              <div class="error-message"> <?php if(isset($pwdmatcherror)) echo "Password does not matches."; ?></div>
           <i id="pwd_repeat_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
         </div>
        <?php
        echo $this->Form->button('Reset', array('type'=>'reset' , 'class'=>"btn btn-danger" ));
        echo $this->Form->button('Change Password',array('type'=>'submit','class' => 'btn btn-primary', 'div'=>false ,  'id' =>"submitbutton"));
        echo $this->Form->end();
        ?>
      </div>
    </div>

  </div>
</div>

<hr>

</div>
