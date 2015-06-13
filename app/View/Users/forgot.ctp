<?php $this->assign('title', 'Forgot Password'); ?>

<div class="container">
  <!-- Example row of columns -->
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-primary">
      <div class="panel-heading">Forgot Password</div>
        <div class="panel-body">

          <?php echo $this->Form->create('User',array('id' => 'forgotpassword_email')); ?>

          <div class="form-group">
                  <div class="form-group has-feedback">
                    <?php
                    echo $this->Form->label('username', 'Email:', array('class'=>'control-label'));
                    echo $this->Form->input("username",array('class' => 'form-control'
                                                              ,'placeholder' => 'Enter your registered email address'
                                                              , 'id' => 'forgotemail','label' => false
                                                              , 'autocomplete'=>"off" , 'type' => 'email' ));

                    ?>
                    <i id="email_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
                  </div>
          </div>

          <?php
          echo $this->Form->button('Forgot Password',array('type'=>'submit',
                                                           'class' => 'btn btn-primary',
                                                           'div'=>false ,  'id' =>"signinpassword"));
          echo $this->Form->end();
          ?>

        </div>
      </div>
    </div>
  </div>

  <hr>

</div>
