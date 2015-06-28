<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $pageTitle;

    public function beforeRender() {
        if ($this->name == 'CakeError') {
            if (AuthComponent::user('id')) {
                $this->layout = 'insidelayout';
                $uid = CakeSession::read("Auth.User.id");
                $params = array(
                    'fields' => array('name', 'verified'),
                    'conditions' => array('id' => $uid),
                );
                $this->loadModel('User');
                $userdata = $this->User->find('first', $params);
                $this->set('name', $userdata['User']['name']);
            } else {
                $this->layout = 'mainlayout';
            }
        }
    }

    public function get_temporary_document_name() {
        $randombytes = openssl_random_pseudo_bytes(10);
        return bin2hex($randombytes) . md5(rand() . time());
    }

    public function generate_token($email, $name) {
        return str_shuffle(hash("sha512", (hash("sha256", $email . $name)) . strval(time()) . md5(rand())));
    }

    public function sendemail($email_view, $email_layout, $userdata, $link, $subject) {
        $this->log('userdata array');
        $this->log($userdata);
        $sign_document_email = new CakeEmail('mandrill_signup');
        $sign_document_email->to($userdata['User']['username']);
        $sign_document_email->subject($subject);
        $sign_document_email->template($email_view, $email_layout)
                ->viewVars(array('link' => $link,
                    'name_of_user' => $userdata['User']['name']));
        return($sign_document_email->send());
    }

    public $components = array(
        'Session',
        'Auth' => array(
            'authenticate' => array(
                'Form' => array(
                    'passwordHasher' => 'Blowfish'
                )
            )
        )
    );

}
