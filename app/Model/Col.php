<?php

class Col extends AppModel
{

  public function getusernames($term=null)
  {
    if(!empty($term)) {
        $users = $this->User->find('list', array(
          'conditions' => array('name' => new MongoRegex("/".$term."/i")),
          'fields' => array('name')
        ));
        $this->log($users);
        return $users;
      }
      return false;
    }


  protected $mongoSchema = array (
    'did' => array (
      'type' => 'string',
      'null' => 'false' ,
      'default' => null
      ),
    'aid' => array (
      'type' => 'string',
      'null' => 'false' ,
      'default' => null
      ),
    'dstatus' => array (
      'type' => 'string',
      'null' => 'false' ,
      'default' => null
      )
    );
}
