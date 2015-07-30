<?php $this->assign('title', 'Admin Panel'); ?>

<div class="container">
    <div class="row">
        <h1>Enter emails for changing account types:</h1>
    </div>
    <div class="row">
        <form>
            <div class="form-group">
                <label for="supportemail">Support Email address</label>
                <input type="email" class="form-control" id="support" placeholder="Enter user email for support">
            </div>
            <div id="results"></div>
            <div id="empty-message"></div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-2 col-md-offset-5">
            <button class="btn btn-success" id="submit" disabled="disabled">Submit</button>
        </div>
    </div>
</div>

<?php
echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('jquery-ui.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('profile_admin.js');
?>