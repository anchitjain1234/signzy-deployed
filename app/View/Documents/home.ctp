<?php
/* display message saved in session if any */
echo $this->Session->flash();
/* create form with proper enctype */
echo $this->Form->create('Documents', array('type' => 'file'));
/* create file input */
echo $this->Form->input('file',array( 'type' => 'file'));
/* create submit button and close form */
echo $this->Form->end('Submit');
?>
