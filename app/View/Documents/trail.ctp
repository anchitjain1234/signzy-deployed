<?php $this->assign('title', 'Document Info'); ?>
<?php echo $this->Html->css('show_document', null, array('inline' => false)); ?>

<?php
/*
 * This function would be used to print the final status of document 
 * which will come on top of timeline.
 */

function content_from_status($status) {
    if ($status === '0') {
        return '<h2>Document is currently prending.</h2>';
    } elseif ($status === '1') {
        return '<h2>Document is successfully closed.</h2>';
    } elseif ($status === '2') {
        return '<h2>Document is void.</h2>';
    } else {
        return '<h2>Document is rejected.</h2>';
    }
}
?>

<?php
/*
 * This function would be used to print the activity which the collabarator has
 * done with the document.
 * If he has dne nothing we would ignore him.
 */

function col_content_from_status($status) {
    if ($status === '1') {
        return ' signed the document.';
    } elseif ($status === '2') {
        return 'voided the document.';
    } else {
        return ' rejected the document.';
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Document Trail</h3>
        </div>
    </div>

    
    <div class="row">
        <div class="col-md-10">

            <div class="timeline-centered">

                <article class="timeline-entry">

                    <div class="timeline-entry-inner">

                        <div class="timeline-icon bg-secondary">
                            <i class="entypo-suitcase"></i>
                        </div>

                        <div class="timeline-label">
                            <?php
                            echo content_from_status($docudata['Document']['status']);
                            ?>
                            <small class="text-muted"><i class="glyphicon glyphicon-time"></i> <?php echo date('l d F, Y ', $docudata['Document']['modified']->sec); ?></small>
                        </div>
                    </div>

                </article>

                <?php
                foreach ($col_data as $col):
                    if ($col['Col']['status'] != 0) {
                        ?>

                        <article class="timeline-entry">

                            <div class="timeline-entry-inner">

                                <div class="timeline-icon bg-info">
                                    <i class="entypo-suitcase"></i>
                                </div>

                                <div class="timeline-label">
                                    <h2><a href="#"><?php echo $id_corresponding_to_name[$col['Col']['uid']] ?></a> <span><?php echo col_content_from_status($col['Col']['status']); ?></span></h2>
                                    <small class="text-muted"><i class="glyphicon glyphicon-time"></i> <?php echo date('l d F, Y ', $col['Col']['modified']->sec); ?></small>
                                </div>
                            </div>

                        </article>
    <?php }endforeach; ?>

                <article class="timeline-entry">

                    <div class="timeline-entry-inner">

                        <div class="timeline-icon bg-info">
                            <i class="entypo-suitcase"></i>
                        </div>

                        <div class="timeline-label">
                            <h2><a href="#"><?php echo $ownerdata['User']['name'] ?></a> <span>invited following users to sign the document</span></h2>
                            <?php
                            foreach ($cols_user_data as $col_user):
                                echo $col_user['User']['name'];
                                echo " , ";
                            endforeach;
                            ?><br>
                            <small class="text-muted"><i class="glyphicon glyphicon-time"></i> <?php echo date('l d F, Y ', $docudata['Document']['created']->sec); ?></small>
                        </div>
                    </div>

                </article>

                <article class="timeline-entry">

                    <div class="timeline-entry-inner">
                        <div class="timeline-icon bg-success">
                            <i class="entypo-feather"></i>
                        </div>

                        <div class="timeline-label">
                            <h2><a href="#"><?php echo $ownerdata['User']['name'] ?></a> <span>created the document</span></h2>
                            <small class="text-muted"><i class="glyphicon glyphicon-time"></i> On <?php echo date('l d F, Y ', $docudata['Document']['created']->sec); ?></small>
                        </div>
                    </div>

                </article>


                <article class="timeline-entry begin">

                    <div class="timeline-entry-inner">

                        <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                            <i class="entypo-flight"></i> +
                        </div>

                    </div>

                </article>

            </div>
        </div>
    </div>


    <?php
    echo $this->Html->script('jquery-1.11.1.min.js');
    echo $this->Html->script('bootstrap.min.js');
    ?>
</div>
