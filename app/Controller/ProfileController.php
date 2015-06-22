<?php

class ProfileController extends AppController {

    public $layout = 'insidelayout';
    public $helpers = array('UploadPack.Upload');

    public function beforeFilter() {
        /*
          To give the name ofthe user to the layout befire the view is loaded.
         */
        $this->loadModel('User');

        $uid = CakeSession::read('Auth.User.id');
        $params = array(
            'conditions' => array('id' => $uid),
        );
        $userdata = $this->User->find('first', $params);
        $this->set('name', $userdata['User']['name']);
        $this->set('userdata', $userdata);
    }

    public function index() {
        $this->loadModel('Document');
        $params = array(
            'conditions' => array('ownerid' => CakeSession::read('Auth.User.id')),
            'limit' => 5,
            'order' => array('created' => -1),
        );
        $this->set('uploads', $this->Document->find('all', $params));

        $params = array(
            'conditions' => array('ownerid' => CakeSession::read('Auth.User.id'), 'status' => '3'),
            'limit' => 5,
            'order' => array('created' => -1),
        );
        $this->set('disputeduploads', $this->Document->find('all', $params));
    }

}
