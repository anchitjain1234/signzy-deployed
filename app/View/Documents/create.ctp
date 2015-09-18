<?php
echo $this->Form->create('Document', array('type' => 'file'));
echo $this->Form->input('name');
echo $this->Form->file('avatar');
echo $this->Form->end(__('Submit'));
?>
