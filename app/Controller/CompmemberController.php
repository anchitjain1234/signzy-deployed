<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
class CompmemberController extends AppController {

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

    public function authorise_user($cid = null) {

        if (!$cid) {
            throw new NotFoundException(__('Invalid URL'));
        }

        $compmembers_info = $this->Compmember->find('count', array('conditions' => array('cid' => $cid, 'status' => Configure::read('legal_head'), 'uid' => CakeSession::read("Auth.User.id"))));

        if (!isset($compmembers_info) || $compmembers_info === 0) {
            throw new NotFoundException(__('Invalid URL'));
        }

        if ($this->request->is('get')) {
            $unauthorised_and_unrejected_signs = $this->Compmember->find('all', array('conditions' => array('cid' => $cid, 'status' => Configure::read('unauth_sign'))));
            $unauthorised_and_unrejected_signs_userids = [];

            foreach ($unauthorised_and_unrejected_signs as $unauthorised_and_unrejected_sign):
                if (!in_array($unauthorised_and_unrejected_sign['Compmember']['id'], $unauthorised_and_unrejected_signs_userids)) {
                    array_push($unauthorised_and_unrejected_signs_userids, array('_id' => new MongoId($unauthorised_and_unrejected_sign['Compmember']['uid'])));
                }
            endforeach;
            if(isset($unauthorised_and_unrejected_sign) && count($unauthorised_and_unrejected_sign) > 0)
            {
                $userinfo = $this->User->find('all', array('conditions' => array('$or' => $unauthorised_and_unrejected_signs_userids)));
                $this->set('unauthorised_signs', $userinfo);
            }
            
            $this->loadModel('Company');
            $company_info = $this->Company->find('first',array('conditions'=>array('id'=>$cid)));
            $this->set('company_info',$company_info);
            $this->set('cid',$cid);
        }
    }

    public function authorize() {
        if ($this->request->is('post')) {

            $this->request->onlyAllow('ajax');
            $this->autorender = false;
            $this->layout = false;
            $this->log($this->request->data);
            $ids = json_decode($this->request->data['ids']);
            $cid = $this->request->data['cid'];
            $this->loadModel('Company');
            $companyinfo = $this->Company->find('first',array('conditions'=>array('id'=>$cid)));
            
            foreach ($ids as $id):
                $existing_row = $this->Compmember->find('first',array('conditions' => array('cid'=>$cid,'uid'=>$id,'status'=>Configure::read('unauth_sign'))));
                if(isset($existing_row) && $existing_row)
                {
                    $this->Compmember->id = $existing_row['Compmember']['id'];
                    $this->Compmember->set('status',Configure::read('auth_sign'));
                    if($this->Compmember->save())
                    {
                        //Sending Confirmation email to the user that he has been confirmed and now can sign the document with the link provided
                        //in previous emails.
                        $userdata=$this->User->find('first',array('conditions'=>array('id'=>$id)));
                        $link = Router::url(array('controller' => 'dashboard', 'action' => 'index'), true);
                        $subject = 'You are now authorized signatory for '.$companyinfo['Company']['name'];
                        $title = 'Authorized signatory confirmed';
                        $content = 'Congrats! You are now authorized signatory for '.$companyinfo['Company']['name'].' .You can '
                                . 'now sign the documents of which you were assigned as signatory on behalf of '.$companyinfo['Company']['name'].
                                '.Check your previous emails to sign those documents or contact the document owner to resend the signing link.';
                        $button_text = 'Visit Verysure dashboard';
                        $this->send_general_email($userdata, $link, $title, $content, $subject, $button_text);
                    }
                    else
                    {
                        /*
                         * Case when data can not be updated.
                         */
                        echo '{"error":1}';
                        exit;
                    }
                }
                else
                {
                    /*
                     * Case when user is not present as unauthorized signatory.
                     */
                    echo '{"error":2}';
                    exit;
                }
            endforeach;
            /*
             * All work done successfully.
             */
            
            /*
             * Send email to legal heads that some users have been added as authorized signatory.
             */
            $legal_head_ids = $this->Compmember->find('first',array('conditions' => array('cid'=>$cid,'status'=>Configure::read('legal_head'))));
            foreach($legal_head_ids as $legal_head):
                $this->log('legal_Head');
                $this->log($legal_head);
                $legal_head_info = $this->User->find('first',array('conditions' => array('id'=>$legal_head['Compmember']['uid'])));
                $link = Router::url(array('controller' => 'dashboard', 'action' => 'index'), true);
                $subject = 'Signatories updated for '.$companyinfo['Company']['name'];
                $title = 'Signatories updated';
                $content = 'Signatories have been updated for '.$companyinfo['Company']['name'].". Click below to visit your admin page"
                        . "to view the changes.";
                $button_text = "Visit admin page";
                $this->send_general_email($legal_head_info, $link, $title, $content, $subject, $button_text);
            endforeach;
            echo '{"success":true}';
        }
    }
    
    public function reject() {
        if ($this->request->is('post')) {

            $this->request->onlyAllow('ajax');
            $this->autorender = false;
            $this->layout = false;

            $ids = json_decode($this->request->data['ids']);
            $cid = $this->request->data['cid'];
            $this->loadModel('Company');
            $companyinfo = $this->Company->find('first',array('conditions'=>array('id'=>$cid)));
            
            foreach ($ids as $id):
                $existing_row = $this->Compmember->find('first',array('conditions' => array('cid'=>$cid,'uid'=>$id,'status'=>Configure::read('unauth_sign'))));
                if(isset($existing_row) && $existing_row)
                {
                    $this->Compmember->id = $existing_row['Compmember']['id'];
                    $this->Compmember->set('status',Configure::read('rejected_sign'));
                    if($this->Compmember->save())
                    {
                        //Sending Confirmation email to the user that he has been confirmed and now can sign the document with the link provided
                        //in previous emails.
                        $userdata=$this->User->find('first',array('conditions'=>array('id'=>$id)));
                        $link = Router::url(array('controller' => 'dashboard', 'action' => 'index'), true);
                        $subject = 'Authorized signatory for '.$companyinfo['Company']['name'].' declined.';
                        $title = 'Authorized signatory declined';
                        $content = 'Unfortunately legal head of '.$companyinfo['Company']['name'].' declined you for authorized signatory.';
                        $button_text = 'Visit Verysure dashboard';
                        $this->send_general_email($userdata, $link, $title, $content, $subject, $button_text);
                    }
                    else
                    {
                        /*
                         * Case when data can not be updated.
                         */
                        echo '{"error":1}';
                        exit;
                    }
                }
                else
                {
                    /*
                     * Case when user is not present as unauthorized signatory.
                     */
                    echo '{"error":2}';
                    exit;
                }
            endforeach;
        }
    }

}
