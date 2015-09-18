<?php $this->assign('title', 'Photo Verification'); ?>

<div class="container">
    <div class="row">
        <h1>Photo Verification</h1>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h4 id="uid" class="<?php echo $profiledata['Profile']['uid'] ?>">Photo Captured during signing</h4>
            <?php $link1 = $this->Html->url(array('controller'=>'profile','action'=>'preview',$coldata['Col']['photocaptured']), $full = TRUE); ?>
            <img src="<?php echo $link1; ?>" alt="Photo Captured"/>
        </div>
        <div class="col-md-6">
            <h4 id="did" class="<?php echo $coldata['Col']['did'] ?>">Verfied Photo</h4>
            <?php $link2 = $this->Html->url(array('controller'=>'profile','action'=>'preview',$profiledata['Profile']['verificationimage']), $full = TRUE); ?>
            <img src="<?php echo $link2; ?>" alt="Verfied Photo"/>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <button class="btn btn-success" id="photos_match" >Photos Match</button>
            <button class="btn btn-danger" id="photos_donotmatch">Photos Do not match</button>
        </div>
    </div>
</div>

<?php
echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('document_photovarification.js');
?>