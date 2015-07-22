<?php

class DashboardController extends AppController {

    public function beforeFilter() {
        /*
          To give the name ofthe user to the layout befire the view is loaded.
         */
        $this->loadModel('User');
        $uid = CakeSession::read('Auth.User.id');

        $params = array(
            'fields' => array('name', 'verified'),
            'conditions' => array('id' => $uid),
        );
        $userdata = $this->User->find('first', $params);
        $this->set('name', $userdata['User']['name']);
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

    if($userdata['User']['verified'] === Configure::read('user_not_verified'))
    {
      //$this->Auth->logout();
      /*
      Give link to user here for resending verification email.
      */
      $this->Session->setFlash(__('Your email hasn\'t been verified.Please
                                   verify it first to continue.'),'flash_error');
      //return $this->redirect(array('controller'=>'users','action' => 'index'));
    }
    /*
    Reduce the SQL queries here by combining them as much as possible
    */
    $this->loadModel('Document');
//    $params = array(
//      'conditions' => array('ownerid' => CakeSession::read("Auth.User.id"),'status' => Configure::read('doc_pending')),
//    );
//    $pendingcount = $this->Document->find('count',$params);
//
//    $params = array(
//      'conditions' => array('ownerid' => CakeSession::read("Auth.User.id"),'status' => Configure::read('doc_completed')),
//    );
//    $completedcount = $this->Document->find('count',$params);
//
//    $params = array(
//      'conditions' => array('ownerid' => CakeSession::read("Auth.User.id"),'status' => Configure::read('doc_void')),
//    );
//    $voidcount = $this->Document->find('count',$params);
//
//    $params = array(
//      'conditions' => array('ownerid' => CakeSession::read("Auth.User.id"),'status' => Configure::read('doc_rejected')),
//    );
//    $disputedcount = $this->Document->find('count',$params);

    $this->loadModel('Col');
    
    $pendingcount = 0;
    $completedcount = 0;
    $voidcount = 0;
    $disputedcount = 0;
    
    $params = array(
      'conditions' => array('uid' => CakeSession::read("Auth.User.id")),
      'fields' => 'did'
    );
    $coldata = $this->Col->find('all',$params);

    if($coldata)
    {
      foreach($coldata as $col):
        $params = array(
          'conditions' => array('id' => $col['Col']['did']),
          'fields' => 'status'
        );
        $docstatus  = $this->Document->find('first',$params);

        if($docstatus['Document']['status'] === Configure::read('doc_pending'))
        {
          $pendingcount += 1;
        }
        elseif ($docstatus['Document']['status'] === Configure::read('doc_completed'))
        {
          $completedcount += 1;
        }
        elseif ($docstatus['Document']['status'] === Configure::read('doc_void'))
        {
          $voidcount += 1;
        }
        elseif ($docstatus['Document']['status'] === Configure::read('doc_rejected'))
        {
          $disputedcount += 1;
        }
      endforeach;
    }
    $this->set('pendingcount',$pendingcount);
    $this->set('completedcount',$completedcount);
    $this->set('voidcount',$voidcount);
    $this->set('disputedcount',$disputedcount);

  }

    /*
      These are dummy functions . Will remove them
     */

    public function upload() {
        $this->layout = 'insidelayout';
    }

    public function docs() {
        $this->layout = 'insidelayout';
    }

}
