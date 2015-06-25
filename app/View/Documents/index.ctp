<?php $this->assign('title', 'VerySure™ : Your Documents!'); ?>
<?php function change_status_number_to_status($status)
{
    if ($status === '0') {
        return '<span class="label label-warning">Pending</span>';
    } elseif ($status === '1') {
        return '<span class="label label-success">Completed</span>';
    } elseif ($status === '2') {
        return '<span class="label label-default">Void</span>';
    } else {
        return '<span class="label label-danger">Disputed</span>';
    }
}
      ?>
<div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1>Documents</h1>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-default">Remind</button>
          <button type="submit" class="btn btn-default">Archive</button>
        </div>
      </div>

      <br/>
      <div class="row">
        <div class="col-md-12">
          <ul class="list-group">
            <?php //echo $this->params['controller'];
                  //echo $this->params['action'];
                  //debug($this->params);
                  ?>
            <?php foreach ($user_documents_data as $user_document): ?>
              <?php 
              echo "<li class=\"list-group-item\">";
                    echo "<div class=\"row\">";
                    echo "<div class=\"col-md-8\"><input type=\"checkbox\" class=\"pull-left\" />&nbsp;";
                    $url = Router::url('/', true)."documents/show/".$user_document['Document']['id'];

                    echo '<a class="pull-left" href="'.$url.'">';
                    echo $user_document['Document']['name'];
                    echo "</a></div>";
                    echo "<div class=\"col-md-2 \">";
                    echo date('Y-M-d h:i:s', $user_document['Document']['created']->sec);
                    echo '</div>';
                    echo '<div class="col-md-2 pull-right">';
                    $status = change_status_number_to_status($user_document['Document']['status']);
                    echo $status;
                    echo '</div>';
                    echo '</li>';
                  endforeach;?>
          </ul>
        </div>
      </div>
