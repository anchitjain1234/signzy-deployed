<?php $this->assign('title', 'Signin'); ?>



<div class="container">
  <!-- Example row of columns -->
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-primary">
      <div class="panel-heading">Sign In</div>
        <div class="panel-body">

          <?php echo $this->Form->create('User',array('id' => 'signinform')); ?>

        <div class="form-group">
                <div class="form-group has-feedback">
                  <?php
                  echo $this->Form->label('username', 'Email:', array('class'=>'control-label'));
                  echo $this->Form->input("username",array('class' => 'form-control','placeholder' => 'Email' , 'id' => 'signinemail','label' => false , 'autocomplete'=>"off" , 'type' => 'email' ));

                  ?>
                  <i id="email_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
                </div>
        </div>

        <div class="form-group">
                <div class="form-group has-feedback ">
                  <?php
                  echo $this->Form->label('password', 'Password:', array('class'=>'control-label'));
                  echo $this->Form->input("password",array('class' => 'form-control','placeholder' => 'Enter password' ,'label' => false , 'id' => 'signinpassword' ));?>

                  <i id="pwd_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
                </div>
              </div>

        <?php
        echo $this->Form->button('Reset', array('type'=>'reset' , 'class'=>"btn btn-danger" ));
            echo $this->Form->button('Sign In',array('type'=>'submit','class' => 'btn btn-primary', 'div'=>false ,  'id' =>"signinpassword"));
            ?>
        <?php echo $this->Form->end(); ?>
      </div>
    </div>
    <?php echo $this->Html->link('Sign Up',array('action'=>'signup'));?>
    <?php echo $this->Html->link('Forgot Password',array('action'=>'forgot'));?>
  </div>
</div>

<hr>

</div>

<?php echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js');
echo $this->Html->script('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.min.js');
echo $this->Html->script('signin.js');
?>
