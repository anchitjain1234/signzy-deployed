<?php
class DashboardController extends AppController
{

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
  	$this->layout = 'insidelayout';
    $this->loadModel('User');
    $uid = CakeSession::read("Auth.User.id");

    $params = array(
      'fields' => array('verified'),
      'conditions' => array('id' => $uid),
    );
    $userdata=$this->User->find('first',$params);

    if($userdata['User']['verified'] === 0)
    {
      echo "adasdasasd";
      //$this->Auth->logout();
      /*
      Give link to user here for resending verification email.
      */
      $this->Session->setFlash(__('Your email hasnt been verified.Please verify it first to continue.'),'flash_error');
      //return $this->redirect(array('controller'=>'users','action' => 'index'));
    }
  }

  public function upload()
  {
  	$this->layout = 'insidelayout';
  }

  public function docs()
  {
  	$this->layout = 'insidelayout';
  }


}

?>
