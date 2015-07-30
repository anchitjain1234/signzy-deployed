<?php $this->assign('title', 'Support Window'); ?>

<div class="container">
    <div class="row">
        <h4>Support requests:</h4>
    </div>
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Link</th>
                    <th>Time of applying</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($requests as $request)
                {
                    ?>
                <tr>
                    <td><?php echo $request['type'];?></td>
                    <td><a href='<?php echo $request['link'];?>'>Click to verify</a></td>
                    <td><?php echo date('Y-M-d h:i:s', $request['time']->sec);?></td>
                </tr>
                <?php
                }?>
            </tbody>
        </table>
    </div>
</div>