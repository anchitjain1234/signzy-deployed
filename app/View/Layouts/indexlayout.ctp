<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $this->fetch('title'); ?></title>
  <!-- Bootstrap -->
  <?php echo $this->Html->css('bootstrap.min' ); ?>
  <?php echo $this->fetch('css'); ?>
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
            'Signzy™',
            array(
              'controller' => 'users',
              'action' => 'index'
              ),
            array('class' => 'navbar-brand')
            );
            ?>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
      <!--
          <form class="navbar-form navbar-right" role="form" method="GET" action="dashboard.html">
            <div class="form-group">
              <input type="text" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button></form> -->


            <?php echo $this->Form->create('User' ,array('id' => 'signinform' , 'class' => 'navbar-form navbar-right')); ?>

            <?php if (isset($signinerror) && $signinerror):
            {
              echo '<div id="signinerror" class="alert alert-danger fade in"><strong>Error! </strong>';
              echo $signinerror;
              echo "</div>";
            }
            ?>

          <?php endif ?>

          <div class="form-group">
            <?php
            echo $this->Form->input("username",array('class' => 'form-control','placeholder' => 'Enter email', 'id' => 'signinemail' ,'label' => false ,'autocomplete'=>"off" , 'type' => 'email'));?>
          </div>
          <div class="form-group">

            <?php
            echo $this->Form->input("password",array('class' => 'form-control','placeholder' => 'Enter password' ,'label' => false , 'id' => 'signinpassword' ));?>






          </div>
          <?php echo $this->Form->button('Sign In',array('type'=>'submit','class' => 'btn btn-success', 'div'=>false ,  'id' =>"signinbutton" ));

          echo $this->Html->link(
            'Sign up',
            array(
              'controller' => 'users',
              'action' => 'signup'
              ),
            array('class' => 'btn btn-primary')
            );?>
            <br>
            <?php echo $this->Html->link('Forgot Password',array('controller'=>'users','action'=>'forgot'));
            echo $this->Form->end();?>


          </div><!--/.navbar-collapse -->
        </div>
      </nav>
      <?php echo $this->Session->flash(); ?>
      <?php echo $this->fetch('content'); ?>
      <?php echo $this->fetch('script'); ?>
      <footer>
        <p class="text-center">&copy; Signzy™ 2014</p>
      </footer>
    </body>
    </html>
