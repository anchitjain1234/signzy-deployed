<?php $i=0;?>
<table class="table">
  <caption>Your Documents</caption>
  <thead>
    <tr>
      <th> S.No. </th>
      <th> Document </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <?php foreach ($user_documents_data as $user_document): ?>
      <td><?php $i+=1; echo h($i); ?></td>

      <td><a href="<?php echo Router::url('/', true).$this->Upload->uploadUrl($user_document,
                                                                          'Document.avatar' , array('urlize' =>false )) ?>">
      <?php echo $user_document['Document']['name']; ?></a></td>
    </tr>
  </tbody>
<?php endforeach; ?>
</table>
