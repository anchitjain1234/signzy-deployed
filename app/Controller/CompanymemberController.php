<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CompanymemberController extends AppController {
    
    public $layout = 'insidelayout';
    public $components = array('RequestHandler');

    public function beforeFilter() {
        /*
          To give the name ofthe user to the layout befire the view is loaded.
         */
        $this->loadModel('User');

        $uid = CakeSession::read("Auth.User.id");
        $params = array(
            'fields' => array('name', 'verified'),
            'conditions' => array('id' => $uid),
        );
        $userdata = $this->User->find('first', $params);
        $this->set('name', $userdata['User']['name']);
    }
    
    public function authorise_user($cid = null){
        if (!$cid) {
            throw new NotFoundException(__('Invalid URL'));
        }
        $document = $this->Document->findById($id);

        if (!$document) {
            throw new NotFoundException(__('Invalid Document'));
        }
    }

}
