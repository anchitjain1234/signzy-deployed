<?php $this->assign('title', 'VerySure™ : Execute Agreements easily!'); ?>
<?php  echo $this->Html->css('signup', null, array('inline' => false)); ?>

<div class="container">


  <div class="row">

    <div class="col-md-4 col-md-offset-2">

      <?php echo $this->Html->link('<div class="panel panel-default">
          <div class="panel-body">
            <h2 class="text-center"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span></h2>
          </div>
          <h4 class="text-center">Send a New Document</h4>
          <br/>
        </div>',array('controller'=>'documents','action'=>'upload2'),array('escape' => FALSE)) ?>
    </div>


    <div class="col-md-4">
      <?php echo $this->Html->link('<div class="panel panel-default">
          <div class="panel-body">
            <h2 class="text-center"><span class="glyphicon glyphicon-file" aria-hidden="true"></span></h2>
          </div>
          <h4 class="text-center">Past Documents</h4>
          <br/>
        </div>',array('controller'=>'documents','action'=>'index'),array('escape' => FALSE)) ?>
    </div>
  </div>


  <div class="row">

    <?php echo $this->Html->link('<div class="col-md-3 text-center hover-highlight">
        <h2><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 0</h2>
        <h4>Action Required</h4>
      </div>','#',array('escape'=>FALSE))?>

    <?php echo $this->Html->link('<div class="col-md-3 text-center hover-highlight">
        <h2><span class="glyphicon glyphicon-time" aria-hidden="true"></span> 0</h2>
        <h4>Waiting for others</h4>
      </div>','#',array('escape'=>FALSE))?>
    <?php echo $this->Html->link('<div class="col-md-3 text-center hover-highlight">
        <h2><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 0</h2>
        <h4>Canceled (Last 1 year)</h4>
      </div>','#',array('escape'=>FALSE))?>
    <?php echo $this->Html->link('<div class="col-md-3 text-center hover-highlight">
        <h2><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 0</h2>
        <h4>Completed (Last 1 year)</h4>
      </div>','#',array('escape'=>FALSE))?>
  </div>
</div>

<?php echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js'); ?>

<script type="text/javascript">
  $(function () {
    $('[data-toggle="popover"]').popover()
  });
  $(".hover-highlight").hover(function(e){
    $(e.currentTarget).addClass("bg-info");
  },function(e){
    $(e.currentTarget).removeClass("bg-info");
  });
</script>
