<form>
<div class="well col-md-5 form-group">
                <input id="demo4" type="text" class="col-md-12 form-control" placeholder="Search names..." autocomplete="off"/>
</div>
</form>
<?php
echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('bootstrap-typeahead.min.js');
?>
<script>
$('#demo4').typeahead({
       ajax: {
         url : 'index.json',
         displayField : 'User[\'name\']'
       }
   });
</script>
