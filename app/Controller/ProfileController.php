<?php

class ProfileController extends AppController
{
  public $layout = 'insidelayout';


  public function beforeFilter()
	{
		/*
		To give the name ofthe user to the layout befire the view is loaded.
		*/
    $this->loadModel('User');

    $uid = CakeSession::read("Auth.User.id");
    $params = array(
      'fields' => array('name', 'verified'),
      'conditions' => array('id' => $uid),
    );
    $userdata=$this->User->find('first',$params);
    $this->set('name',$userdata['User']['name']);
	}


  public function index()
  {
    
  }

}

?>
