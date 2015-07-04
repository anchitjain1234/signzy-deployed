<?php

class CompanyController extends AppController {

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

    public function index() {
        
    }

    public function company_search() {
        $this->autorender = false;
        $this->layout = false;
        $this->request->onlyAllow('ajax');
        if ($this->request->is('ajax')) {
            $term = $this->request->query('term');
            $companyNames = $this->Company->find('list', array(
                'conditions' => array('name' => new MongoRegex("/" . $term . "/i")),
                'fields' => array('name')
            ));
            $this->set(compact('companyNames'));
            $this->set('_serialize', 'companyNames');
        }
    }

}
