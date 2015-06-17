<?php

class ColController extends AppController
{

  public $layout = 'insidelayout';
  public $components = array('RequestHandler');
  //DebugKit.Toolbar
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
    if ($this->request->is('ajax')) {
        $term = $this->request->query('term');
        $userNames = $this->User->find('list', array(
          'conditions' => array('username' => new MongoRegex("/".$term."/i")),
          'fields' => array('username')
        ));

        $this->set(compact('userNames'));
        $this->set('_serialize', 'userNames');
        $this->log($userNames);
      }
  }

  public function search_user()
  {
    if($this->request->is('ajax'))
    {
      echo "going";
      $parameters= array(
        'conditions' => array('name' => new MongoRegex($term)),
        'fields' => array('name')
      );
      $search_data = $this->User->find('all',$params);

    }
  }

  public function index2()
  {

  }

  public function test()
  {

  }

}

?>
