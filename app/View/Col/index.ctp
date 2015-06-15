
<div class="ui-widget">
<label for="tags">Tags: </label>
<input id="tags">
</div>
  <?php
    //let's load jquery libs from google
    $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', array('inline' => false));
    $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js', array('inline' => false));
    $this->Html->script('colindex.js', array('inline' => false));
    //load file for this view to work on 'autocomplete' field

    //form with autocomplete class field
    ?>
