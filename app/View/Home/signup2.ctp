<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VerySure™ : Execute Agreements easily!</title>
    <script type="text/javascript">
    var fl = 0;
    </script>
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
              <form role="form" method="POST" action="dashboard.html">
                <div class="form-group">
                  <div class="form-group has-success has-feedback">
                    <label for="username">Full Name:</label>
                    <input name = "data[Home][" type="username" class="form-control" id="username" placeholder="Name, as per your legal documents" />
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-group has-success has-feedback">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter email" aria-describedby="inputSuccess2Status" aria-describedby="inputSuccess2Status">
                    <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="pwd">Password:</label>
                  <input type="password" class="form-control" id="password" placeholder="Enter password" onkeyup="pwd_check()">
                  
                
                  
                </div>
                <div class="form-group has-feedback ">
                  <label for="pwd">Confirm Password:</label>
                  <input type="password" class="form-control" id="renter_password" placeholder="Enter password again" onkeyup="pwd_confirmation()" >
                  <div id="feedback_for_pwd_confirm">
                  </div>
                </div>

                <div class="form-group">
                  <div class="form-group has-success has-feedback">
                    
                  </div>
                </div>

                <button type="submit" class="btn btn-primary">Signup</button>
                <button type="reset" class="btn btn-danger">Reset</button>
              </form>
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
          ?>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript">

    function pwd_check()
    {
      var fl=1;
    }

    </script>
    <script type="text/javascript">

    function pwd_confirmation()
    {
      var pwd=document.getElementById("password").value;
      var pwd_reenter=document.getElementById("renter_password").value;
      console.log("pwd="+pwd+"pwd_reenter="+pwd_reenter);

      if(pwd !== pwd_reenter)
      {
        document.getElementById("feedback_for_pwd_confirm").innerHTML = '<span class="glyphicon glyphicon-remove form-control-feedback"></span>';
      }
      else
      {
        document.getElementById("feedback_for_pwd_confirm").innerHTML = '<span class="glyphicon glyphicon-ok form-control-feedback"></span>';
      };
    }

     jQuery(document).ready(function () {
    "use strict";
    var options = {};
    options.common = {
        usernameField : "#email",
        onLoad: function () {
            $('#messages').text('Start typing password');
        },
        onKeyUp: function (evt, data) {
            $("#length-help-text").text("Current length: " + $(evt.target).val().length + " and score: " + data.score);
            if(data.score>=15)
            {
              $('#messages').text('Good to go');
            }
            else
            {
              $('#messages').text('Please change password');
            }
        },
        debug : true
    };
    options.ui = {
        showPopover: false,
        showErrors: false,
        useVerdictCssClass: true,
        showVerdictsInsideProgressBar: true,
        showProgressBar: true,
        verdicts: [
            "<span class='glyphicon glyphicon-remove'></span> Weak",
            "<span class='glyphicon glyphicon-exclamation-sign'></span> Normal",
            "Medium",
            "<span class='glyphicon glyphicon-thumbs-up'></span> Strong",
            "<span class='glyphicon glyphicon-ok'></span> Very Strong"],
        spanError : function (options, key) {
          var text = options.ui.errorMessages[key];
          return '<span style="color: #d52929">' + text + '</span>';
          },
        popoverError:function (errors) {
          var message = "<div><ul class='error-list' style='margin-bottom: 0;'>";
          jQuery.each(errors, function (idx, err) {
            if(err != '<span style="color: #d52929">undefined</span>')
            {
              message += "<li>" + err + "</li>"; 
            } 
            
          });
            message += "</ul></div>";
            return message;
          }
    };
    options.rules = {
        activated: {
            wordNotEmail: true,
            wordLength: true,
            wordSimilarToUsername: true,
            wordSequences: true,
            wordTwoCharacterClasses: true,
            wordRepetitions: true,
            wordOneSpecialChar: true,
            wordLetterNumberCombo: true,

        }
    };
    $('#password').pwstrength(options);
});

    </script>
    <script type="text/javascript">
      $(function () {
        $('[data-toggle="popover"]').popover()
      });
    </script>
  </body>
</html>