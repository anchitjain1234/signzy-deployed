<?php echo $this->Form->create('Document', array('type' => 'file')); ?>
    <?php echo $this->Form->input('Document.username'); ?>
    <?php echo $this->Form->input('Document.photo', array('type' => 'file')); ?>
    <?php echo $this->Form->input('Document.photo_dir', array('type' => 'hidden')); ?>
<?php echo $this->Form->end(__('Submit')); ?>
