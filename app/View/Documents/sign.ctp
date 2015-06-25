<?php $this->assign('title', 'Sign Document'); ?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h3>Please Review and act on the document</h3>
    </div>
  </div>

  <div class="row">
    <div class="col-md-1"><?php echo $this->Html->image('profile_new.png' , array('alt' => 'Document')); ?></div>
    <div class="col-md-3"><h5><?php echo $name ?></h5><h5>Your Affiliated Company</h5><h6></h6></div>
    <div class="col-md-8 text-right">
      <?php $link=Router::url('/', true)."uploads/".$document['Document']['originalname'];?>
      <a href="<?php echo $link; ?>">Download</a> | <?php echo $this->Html->link('Trail', array('controller'=>'documents','action'=>'trail',$document['Document']['id'])); ?>
    </div>
  </div>

  <div class="row top-buffer">
    <div class="col-md-12 bg-highlight" style="height:400px;">
      <?php
        if(isset($document))
        {

          echo "<embed src='".$link."' width = '1100' height = '380'></embed>";
        }
        else
        {
          echo "<div class=\"container\" >
                  <div class=\"row align-center\">
                    Document to be signed will appear here.
                  </div>
                </div>";
        }
      ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 text-center">
      <form class="form-inline">
        <div class="form-group">
          <label>Biometric Signature</label>
          <select class="form-control" id="biometric_type">
                  <!-- <option>Not required</option> -->
                  <option value="voicescan">Voicescan</option>
                  <option value="facescan">Facescan</option>
                </select>
          </div>
        <button class="btn btn-primary" type="submit" id="sign">Sign</button>
      </form>
    </div>
  </div>

  <div class="row top-buffer">
    <div class="col-md-12 text-center">
      <button class="btn btn-success" type="submit" id="accept">Accept</button>
      <button class="btn btn-danger" type="submit" id="decline">Decline</button>
    </div>
  </div>

  <div class="modal fade" id="modal_voicescan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Voicescan</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h5>Place speak something on the mic, for voice scanning</h5>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_facescan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Facescan</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h5>Place your face in front of the camera</h5>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="camshot"></div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Capture</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_takesnap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Face Snap</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h5>Place your face in front of the camera</h5>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="camshot"></div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary">Send</button>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Decline Message</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-offset-2 col-md-8 text-center">
            <textarea placeholder="Please add a message before declining..." class="form-control"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" id="void_sign">Void</button>
        <button type="button" class="btn btn-danger" id="decline_sign">Decline</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_accept" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Are you sure to sign the document?(You can revert back later)</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-offset-2 col-md-8 text-center">
            <textarea placeholder="Feedback for document owner.." class="form-control"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="sure_success">Sure</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Not Sure</button>
      </div>
    </div>
  </div>
</div>

<?php echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('jquery.webcam.min.js');
echo $this->Html->script('sign.js');?>
