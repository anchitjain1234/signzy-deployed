<?php $this->assign('title', $docudata['Document']['name']); ?>

<div class="container">
      <div class="row">
        <div class="col-md-12">
          <h3><?php echo $docudata['Document']['name'] ?></h3>
        </div>
      </div>

      <div class="row">
        <div class="col-md-1"><?php echo $this->Html->image('profile_new.png', array('alt' => 'Profile Picture')); ?></div>
        <div class="col-md-3"><h5><?php echo $name; ?></h5><h5>ABC Pvt. Ltd.</h5><h6></h6></div>
        <div class="col-md-8 text-right">
          <?php $name_seperated = explode(".", $docudata['Document']['originalname']); 
 $name_front="";
              for( $i=0;$i<count($name_seperated)-1;$i++)
              {
                  $name_front .= $name_seperated[$i];
              }
        ?>
      <?php $link=Router::url('/', true)."documents/preview?name=".$name_front."&type=".$name_seperated[count($name_seperated)-1];
            $download_link=Router::url('/', true)."documents/download?name=".$name_front."&type=".$name_seperated[count($name_seperated)-1];
      ?>
          <a href="<?php echo $download_link; ?>">Download</a> | <?php echo $this->Html->link('Trail', array('controller' => 'documents', 'action' => 'trail', $docudata['Document']['id'])); ?>
        </div>
      </div>

      <div class="row top-buffer">
        <div class="col-md-12 bg-highlight" style="height:400px;">
          <?php echo "<iframe src='".$link."' width = '1100' height = '380'></iframe>"; ?>
        </div>
      </div>

      <?php if ($userid === $docudata['Document']['ownerid']) {
    echo '<div class="row top-buffer">';
    echo '<div class="col-md-12 text-center">';
    echo $this->Html->link('Edit', array('controller' => 'documents', 'action' => 'edit', $docudata['Document']['id']), array('class' => 'btn btn-warning '));
    echo '<button type="button" class="btn btn-danger" id="delete">Delete</button>';
    echo '</div>';
    echo '</div>';
}
            ?>

      <hr>
    </div>

    <?php if ($userid === $docudata['Document']['ownerid']): ?>
    <div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Are you sure to delete <?php echo $docudata['Document']['name'] ?>?(You <strong>can not revert</strong> back later)</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12 docidholder" id="<?php echo $docudata['Document']['id'] ?>">
                Please type the name of the document
              </div>
              <div class="col-md-12 text-center">
                <textarea placeholder="Feedback for document owner.." class="form-control" id="delete_docname_area"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="delete_delete">Delete</button>
            <button type="button" class="btn btn-success delete_donot" data-dismiss="modal" id="<?php echo $docudata['Document']['name'] ?>">Do Not Delete</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif;?>


    <?php echo $this->Html->script('jquery-1.11.1.min.js');
          echo $this->Html->script('bootstrap.min.js');
    if ($userid === $docudata['Document']['ownerid']) {
        echo $this->Html->script('show_document.js');
    }?>
