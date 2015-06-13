<?php $this->assign('title', 'Upload Documents'); ?>

<div class="container">
  <div class="row">
    <div class="col-md-6" style="height:500px;overflow-y:scroll;" id="upload_preview">
        <!-- <img src="img/sample_nda.png" /> -->
        <?php
          if(isset($document))
          {

            $link=$this->Upload->uploadUrl($document, 'Document.avatar' , array('urlize' =>'true'));
            echo "<embed src='".$link."' width = '540' height = '490'></embed>";
          }
        ?>

    </div>
    <div class="col-md-6">
          <!-- <img src="img/sample_nda.png" /> -->
          <?php
          echo $this->Form->create('Document', array('type' => 'file'));
          echo $this->Form->input('name');
          echo $this->Form->file('avatar');
          echo $this->Form->end(__('Submit'));
          ?>
          <!--
      <?/*php echo $this->Form->create('Document',array('type' => 'file')); */?>
        <div class="form-group">
          <div class="form-group has-feedback">

            <?php
            /*
            echo $this->Form->label('name', 'Document Name:', array('class'=>'control-label'));
            echo $this->Form->input("name",array('class' => 'form-control','placeholder' => 'Name of Document' ,
                                                 'id' => 'userusername','label' => false ,
                                                 'autocomplete'=>"off" ));*/
            ?>
          </div>
        </div>
        <div class="form-group">
          <div class="form-group has-feedback">
            <?php/*
            echo $this->Form->label('avatar', 'Document:', array('class'=>'control-label'));
            echo $this->Form->file("name",array('class' => 'form-control','placeholder' => 'Upload' ,
                                                 'id' => 'userusername','label' => false ,
                                                 'autocomplete'=>"off" ));*/
            ?>
          </div>
        </div>
        <?php/*
        echo $this->Form->button('Upload',array('type'=>'submit','class' => 'btn btn-primary', 'div'=>false ,  'id' =>"submitbutton"));
        echo $this->Form->end();*/
        ?>-->
    </div>
  </div>
</div>
