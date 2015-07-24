<?php

require '../vendor/autoload.php';

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

CakeLog::config('default', array(
    'engine' => 'File'
));
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

    public function get_aws_sdk() {
        $sharedConfig = [
            'region' => 'us-west-2',
            'version' => 'latest'
        ];
        $sdk = new Aws\Sdk($sharedConfig);
        return $sdk;
    }

    public function get_temporary_document_name() {
        /*
         * Generating name which would be used to save the uploaded file in upload location.
         */
        $randombytes = openssl_random_pseudo_bytes(10);
        return bin2hex($randombytes) . md5(rand() . time());
    }

    public function generate_token($email, $name) {
        /*
         * Generating token as shuffled string of
         * sha 512 hash of -> (sha 256 hash of username(i.e. email) + name )+ current timestamp
         * + md5 hash of random number
         */
        return str_shuffle(hash("sha512", (hash("sha256", $email . $name)) . strval(time()) . md5(rand())));
    }

    public function sendemail($email_view, $email_layout, $userdata, $link, $subject) {
        $sign_document_email = new CakeEmail('mandrill_signup');
        $sign_document_email->to($userdata['User']['username']);
        $sign_document_email->subject($subject);
        $sign_document_email->template($email_view, $email_layout)
                ->viewVars(array('link' => $link,
                    'name_of_user' => $userdata['User']['name']));
        return($sign_document_email->send());
    }
    
    public function add_email_message_sqs($email_message,$sqsclient,$queueurl,$userdata)
    {
        $email_message['user_username'] = $userdata['User']['username'];
        $email_message['user_name'] = $userdata['User']['name'];
        $email_json = json_encode($email_message);
        
        /*
         * Add code here for the case when message cant be added successfully into SQS.
         */
        $sqsclient->sendMessage(array(
            'QueueUrl' => $queueurl,
            'MessageBody' => $email_json,
        ));
    }
    
    public function add_upload_message_sqs($docname,$sqsclient,$queueurl)
    {
        $upload_message['docname']=$docname;
        $upload_json = json_encode($upload_message);
        
        $sqsclient->sendMessage(array(
            'QueueUrl' => $queueurl,
            'MessageBody' => $upload_json,
        ));
    }
    
    public function upload_s3_from_sqs()
    {
        $url = Router::url(array('controller' => 'users', 'action' => 'upload_doc'), true);
        $command = "wget -qO- ".$url."  > /dev/null 2>/dev/null &";
        exec($command);
    }

    public function send_email_from_sqs()
    {
        $url = Router::url(array('controller' => 'users', 'action' => 'send_email'), true);
        $command = "wget -qO- ".$url."  > /dev/null 2>/dev/null &";
        exec($command);
    }

    public function send_general_email($userdata, $link, $title, $content, $subject, $button_text) {
        $email = new CakeEmail('mandrill_signup');
        $email->template('general_email', 'notification_email_layout')
                ->viewVars(array('link' => $link,
                    'name_of_user' => $userdata['User']['name'],
                    'title_for_email' => $title,
                    'content_for_email' => $content,
                    'button_text' => $button_text));
        $email->to($userdata['User']['username']);
        $email->subject($subject);
        return($email->send());
    }

    public function company_name_from_email_check($email, $company_name) {
        /*
         * Getting the text from email address between "@" and first "."
         * i.e. a@asdasd.com will give asdasd
         * f@as.we.23.com will give as
         */
        preg_match("/\@(.*?)\./", $email, $regex_output);
        /*
         * Here flaw is that we are getting text only till first "."
         * instead we should get till last "." so that false negatives can be reduced but 
         * this would be increasing false positives.
         */
        $company_name_from_email = strtolower($regex_output[1]);

        /*
         * Getting similarity between lowered case of 
         */
        similar_text($company_name_from_email, strtolower($company_name), $percent);
        return $percent;
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
