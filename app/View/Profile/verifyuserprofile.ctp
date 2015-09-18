<?php $this->assign('title', 'User Profile Verification'); ?>


<div class="container" >
    <div class="row">
        <h1 class="useridh1" id="<?php echo $userinfo['Profile']['uid']; ?>">User verification.</h1>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h4>Face detected</h4>
            <?php $link = $this->Html->url(array('controller'=>'profile','action'=>'preview',$userinfo['Profile']['verificationimage']), $full = TRUE); ?>
            <img src="<?php echo $link; ?>" alt="face detected"/>
        </div>
        <div class="col-md-6">
            <h4 class="pid" id="<?php echo $userinfo['Profile']['id']; ?>">Profile Picture</h4>
            <?php $link2 = $this->Html->url(array('controller'=>'profile','action'=>'preview',$userinfo['Profile']['profilepicture']), $full = TRUE); ?>
           <img src="<?php echo $link2; ?>" alt="face detected"/>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <button class="btn btn-success" id="verify_user" >Verify</button>
            <button class="btn btn-danger" id="reject_user">Reject</button>
        </div>
    </div>
</div>

<?php
echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('profile_verifyuserprofile.js');
?>