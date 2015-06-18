<?php $this->assign('title', 'Profile'); ?>

<?php function change_status_number_to_status($status)
      {
        if($status === "0")
        {
          return "<span class=\"label label-warning\">Pending</span>";
        }
        elseif ($status === "1")
        {
          return "<span class=\"label label-success\">Completed</span>";
        }
        elseif ($status === "2")
        {
          return "<span class=\"label label-default\">Void</span>";
        }
        else
        {
          return "<span class=\"label label-danger\">Disputed</span>";
        }
      }
      ?>

<div class="container">
    <div class="alert alert-warning alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      Your account is <strong>not yet verified</strong>. Please verify by uploading your pan card or passport <a href="#">click here</a>!
    </div>

      <div class="row">
        <div class="col-md-2">
        <?php  echo $this->Html->image('profile.jpg' , array('alt' => 'Profile Image' , 'class'=>'img-circle')); ?>
        </div>
        <div class="col-md-10">
            <div class="row">
              <div class="col-md-12">
                <div><h1 style="display:inline-block;"><?php echo $name; ?></h1>
                  <?php
                  if($userdata['User']['verified'] === 1)
                  {
                    echo "<span class=\"label label-success\">Verified</span></div>";
                  }
                  else
                  {
                    echo "<span class=\"label label-danger\">Not Verified</span></div>";
                  }
                  ?>

                <span>Your description</span>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <label>Authorized Signatory: </label>
                <span class="label label-info">Company 1</span>
                <span class="label label-info">Company 2</span>
                <span class="label label-info">Company 3</span>
                <span class="label label-info">Company 4</span>
              </div>
            </div>
        </div>
      </div>
      <br/>
      <div class="row">
        <div class="col-md-6" >
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Recent Uploads</h3>
            </div>
            <table class="table">
              <?php
                foreach ($uploads as $upload):
                  echo "<tr><td>";
                  //$url="#";
                  $url = Router::url('/', true).$this->Upload->uploadUrl($upload,'Document.avatar' , array('urlize' =>false ));
                  echo "<a href=\"".$url."\">";
                  echo $upload['Document']['name'];
                  echo "</a></td><td></td><td>";
                  $status=change_status_number_to_status($upload['Document']['status']);
                  echo $status;
                  echo "</td>";
                endforeach;
              ?>
              <tr>
                <td colspan="3"><?php echo $this->Html->link('View all >>',array('controller'=>'documents'));?></td>
              </tr>
            </table>
          </div>
        </div>

        <div class="col-md-6" >
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Disputes</h3>
            </div>
            <table class="table">
              <?php
                foreach ($disputeduploads as $upload):
                  echo "<tr><td>";
                  //$url="#";
                  $url = Router::url('/', true).$this->Upload->uploadUrl($upload,'Document.avatar' , array('urlize' =>false ));
                  echo "<a href=\"".$url."\">";
                  echo $upload['Document']['name'];
                  echo "</a></td><td></td><td>";
                  $status=change_status_number_to_status($upload['Document']['status']);
                  echo $status;
                  echo "</td>";
                endforeach;
              ?>
            </table>
          </div>
        </div>


      </div>
    </div>

    <div class="container">
      <hr>
      <footer>
        <p>&copy; VerySure™ 2014</p>
      </footer>
    </div>
