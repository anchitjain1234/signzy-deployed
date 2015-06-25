<?php $this->assign('title', $docudata['Document']['name']); ?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 text-center">
      <h3><?php echo $docudata['Document']['name'] ?></h3>
    </div>
  </div>

  <br><br>
  <div class="row">
    <div class="col-sm-3 col-xs-7"><h4>Edit Document Name :</h4></div>
    <div class="col-xs-5"><input type="text" name="new_document_name" id="new_document_name"
      placeholder="Enter new name here ..." class="form-control" /></div>
  </div>
  <hr>

  <div class="row">
    <div class="col-sm-3 col-xs-7"><h4>Edit signatories</h4> </div>
    <div class="col-xs-5">
    <ul class="list-group" id="signatory_holder">
    <?php if(isset($cols)){foreach($cols as $col): ?>
      <li class='list-group-item'>
      <?php echo $col['User']['name']; ?>
      (
      <?php echo $col['User']['username']; ?>
      )
      <span class='label label-primary'>Biometric required</span>
      <a href='#' class='pull-right delete_signatory' id="<?php echo $col['User']['username']; ?>">
        <span class='glyphicon glyphicon-remove'></span>
        </a>
        </li>
      <?php endforeach;} ?>
    </ul>
      <button id="<?php echo $useremail ?>" type="button" class="btn btn-primary signatory_button" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus"></span>Add Signatory</button>
    </div>
  </div>

  <hr>
  <div class="col-sm-4 col-sm-offset-4 col-xs-6 col-xs-offset-3 docidholder" id="<?php echo $docudata['Document']['id'] ?>">
    <button value="Submit Changes" class="btn btn-success" id="change_document">Submit Changes</button>
  </div>



  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog">
 <div class="modal-content">
   <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     <h4 class="modal-title" id="myModalLabel">Add a signatory</h4>
   </div>
   <div class="modal-body">

             <div class="form-group">
               <?php
               echo $this->Form->label('usernsme', 'Email:', array('class' => 'control-label')); ?>
                 <?php echo $this->Form->input('username', array('class' => 'form-control', 'placeholder' => 'Email address', 'id' => 'email_search', 'label' => false, 'autocomplete' => 'off')); ?>
                 <div id="results"></div>
                 <div id="empty-message"></div>
             </div>
             <!--
             <div class="form-group">
               <label for="inputEmail3" class="col-sm-4 control-label">Authorized Company</label>

               <div class="col-sm-4">
                 <input type="radio" name="company"> Company 1 <br/>
                 <input type="radio" name="company"> Company 2 <br/>
                 <input type="radio" name="company"> Company 3 <br/>

                 <br/>
                 <button type="button" class="btn btn-default" id="add_company"><span class="glyphicon glyphicon-plus"></span>Add</button>
               </div>
               <div class="col-sm-4"></div>
             </div>

             <div class="form-group">
               <div class="col-sm-offset-4 col-sm-8">
                 <div class="checkbox">
                   <label>
                     <input type="checkbox"> Biometric required?
                   </label>
                 </div>
               </div>
             </div>
           -->
   </div>
   <div class="modal-footer">
     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     <button type="button" class="btn btn-primary" id="add_button">Add</button>
   </div>
 </div>
 </div>
 </div>
</div>


<?php echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('jquery-ui.min.js');
echo $this->Html->script('tagmanager.js');
echo $this->Html->script('edit_document.js');?>
