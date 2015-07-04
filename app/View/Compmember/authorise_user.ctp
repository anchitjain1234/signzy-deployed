<?php $this->assign('title', 'Authorise User for Signing'); ?>

<div class="container">
    <div id="alertdiv">

    </div>
    <div class="row">
        <div class="col-sm-12 text-center">
            <h1>User Authorization</h1>
        </div>
    </div>

    <div class="row">
        <h4>Following users require access to sign documents on <b><u><?php echo $company_info['Company']['name']?></u></b>'s behalf:</h4>
    </div>
    
    <?php
    if(isset($unauthorised_signs))
    {
        ?>
    <div class="row">
        <table class="table table-hover" id="<?php echo $cid; ?>">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Select</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($unauthorised_signs as $unauthorised_sign):
                    ?>
                    <tr>
                        <td><?php echo $unauthorised_sign['User']['name'] ?></td>
                        <td><?php echo $unauthorised_sign['User']['username'] ?></td>
                        <td><div class="checkbox col-md-8 col-md-offset-2"><input type="checkbox" name="users_checkbox"  id="<?php echo $unauthorised_sign['User']['id'];  ?>"></div></td>
                    </tr>
                    <?php
                endforeach;
                ?>
            </tbody>
        </table>
    </div>
    <?php
    }
    else
    {
        ?>
    <div class="row">
        <h3>No users require authorization access.</h3>
    </div>
    <?php
    }
    ?>
    

    <div class="row col-sm-3 col-sm-offset-5">
        <button class="btn btn-success" id="authorize_user_btn" disabled="disabled">Authorize</button>
        <button class="btn btn-danger" id="reject_user_btn" disabled="disabled">Reject</button>
    </div>
</div>

<?php
echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('authorise_user.js');
?>