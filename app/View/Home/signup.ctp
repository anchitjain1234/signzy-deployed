<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VerySure™ : Execute Agreements easily!</title>
  <!-- Bootstrap -->
  <?php echo $this->Html->css('bootstrap.min'); 
        echo $this->Html->css('signup')
  ?>

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->

      <style type="text/css">
        body {
          padding-top: 70px;
        }
      </style>
    </head>
    <body>

      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <?php
            echo $this->Html->link(
              'VerySure™',
              array(
                'controller' => 'home',
                'action' => 'index'
                ),
              array('class' => 'navbar-brand')
              );
              ?>
            </div>
          </div>
        </nav>

        <!-- Main jumbotron for a primary marketing message or call to action -->
<!--     <div class="jumbotron">
      <div class="container">
        <h1>Execute Agreements!</h1>
        <p>VerySure™ allows you to sign agreements faster, cheaper and securly!</p>
        <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more &raquo;</a></p>
      </div>
    </div> -->

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4 col-md-offset-4">
          <div class="panel panel-primary">
            <div class="panel-heading">Sign Up</div>
            <div class="panel-body">
              <?php echo $this->Form->create('Home'); ?>
              <div class="form-group">
                <div class="form-group has-feedback">
                  <?php
                  echo $this->Form->label('username', 'Full Name:', array('class'=>'control-label')); 
                  echo $this->Form->input("username",array('class' => 'form-control','placeholder' => 'Name, as per your legal documents' ,'label' => false));?>
                  <i class="glyphicon glyphicon-user form-control-feedback"></i>
                </div>
              </div>
              <div class="form-group">
                <div class="form-group has-feedback ">
                  <?php
                  echo $this->Form->label('email', 'Email:', array('class'=>'control-label')); 
                  echo $this->Form->input("email",array('class' => 'form-control','placeholder' => 'Enter email' ,'label' => false));?>
                  <i class="glyphicon glyphicon-ok form-control-feedback"></i>
                </div>
              </div>
              <div class="form-group">
                <div class="form-group has-feedback ">
                  <?php
                  echo $this->Form->label('password', 'Password:', array('class'=>'control-label')); 
                  echo $this->Form->input("password",array('class' => 'form-control','placeholder' => 'Enter password' ,'label' => false , 'id' => 'password' ));?>
                  <i id="pwd_glyph" class="glyphicon glyphicon-ok form-control-feedback"></i>
                </div>


                

              </div>
              <div class="form-group has-feedback ">
               <?php
               echo $this->Form->label('password_repeat', 'Confirm Password:', array('class'=>'control-label')); 
               echo $this->Form->input("password_repeat",array('class' => 'form-control','type' => 'password' ,'placeholder' => 'Enter password again' ,'label' => false , 'id' => 'renter_password' , 'onkeyup' => 'pwd_confirmation()'));?>
               <i id="pwd_repeat_glyph" class="glyphicon glyphicon-remove form-control-feedback"></i>
             </div>

             <div class="form-group">
              <div class="form-group has-success has-feedback">

              </div>
            </div>
            <?php
            echo $this->Form->button('Reset', array('type'=>'reset' , 'class'=>"btn btn-danger" )); 
            echo $this->Form->button('Sign Up',array('type'=>'submit','class' => 'btn btn-primary', 'div'=>false ,  'id' =>"submitbutton"));
            echo $this->Form->end();
            ?>
            <div id="messages"></div>
            <div id="length-help-text"></div>
          </div>
        </div>

      </div>
    </div>

    <hr>

    <footer>
      <p>&copy; VerySure™ 2014</p>
    </footer>
  </div> <!-- /container -->

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <?php echo $this->Html->script('jquery-1.11.1.min.js'); 
  echo $this->Html->script('bootstrap.min.js');
  echo $this->Html->script('pwstrength.js');
  echo $this->Html->script('signup.js');
  ?>
  <!-- Include all compiled plugins (below), or include individual files as needed -->

</body>
</html>