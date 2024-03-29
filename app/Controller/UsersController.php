<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class UsersController extends AppController {

    public function beforeFilter() {
        /*
          To allow all the actions present in UsersController without authorization.
         */
        $this->Auth->allow();
    }

    public function index() {
        $this->layout = 'indexlayout';

        /*
          If user is already logged in redirect user to dashboard.
         */
        if (AuthComponent::user('id')) {
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }

        /*
          Getting user authenticated.
         */
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Session->setFlash(__('Please check entered details.'), 'flash_error');
            }
        }
    }

    public function signup() {
        $this->layout = 'mainlayout';

        /*
          pwdmatcherror is used to set error in case ConfirmPassword doesnt matches Password. It has been seperately
          used because ConfirmPassword hasn't been generated using CakePHP as it was saving ConfirmPassword also in
          database
         */
        $this->set('pwdmatcherror', false);

        if ($this->request->is('post')) {
            $this->User->create();

            /* verified status will be false by default. 0 = false 1 = true */
            $this->request->data['User']['verified'] = Configure::read('user_not_verified');
            $this->User->set($this->request->data);

            if ($this->User->validates()) {
                if ($this->request->data['renter_password'] === $this->request->data['User']['password']) {

                    /*
                     * Checking if company name is set
                     */
                    if (isset($this->request->data['User']['companyname'])) {
                        $this->loadModel('Company');
                        $this->Company->set('name', $this->request->data['User']['companyname']);

                        /*
                         * If company name entered follows our validation rules or not.
                         */
                        if ($this->Company->validates()) {
                            $check_percentage = $this->company_name_from_email_check($this->request->data['User']['username'], $this->request->data['User']['companyname']);
                            $this->Company->save();
                            /*
                             * Change this parameter in future as current implementation is flawed.
                             */
//                            if ($check_percentage >= 70) {
//                                $this->Company->save();
//                            } else {
//                                $this->set('companymatcherror', true);
//                                return $this->Session->setFlash(__('Please check highlighted fields.'), 'flash_warning');
//                            }
                        } else {
                            $errors = $this->Company->validationErrors;
                            $this->set('companyuniquerror', true);
                            $this->set('uniquerrorcontent', $errors['name']['0']);
                            return $this->Session->setFlash(__('Please check highlighted fields.'), 'flash_warning');
                        }
                    }
                    /*
                     * Generating token which would be used for email verification.
                     * Written in AppController
                     */
                    $token = $this->generate_token($this->request->data['User']['username'], $this->request->data['User']['name']);
                    $this->request->data['User']['token'] = $token;
                    
                    /*
                     * Setting the user to customer by default.
                     */
                    $this->request->data['User']['type'] = Configure::read('customer');
                    if ($this->User->save($this->request->data)) {

                        if (isset($this->request->data['User']['companyname'])) {
                            $this->loadModel('Compmember');
                            $company_data = $this->Company->find('first', array('conditions' => array('name' => $this->request->data['User']['companyname'])));
                            $this->Compmember->create();
                            $this->Compmember->set('cid', $company_data['Company']['id']);
                            $userdata = $this->User->find('first', array('conditions' => array('username' => $this->request->data['User']['username'])));
                            $this->Compmember->set('uid', $userdata['User']['id']);
                            $this->Compmember->set('status', Configure::read('legal_head'));
                            if ($this->Compmember->save()) {
                                /*
                                  Send verification email
                                 */
                                $email_verification_link = Router::url(array('controller' => 'users',
                                            'action' => 'verify',
                                            '?' => [
                                                'username' => $this->request->data['User']['username'], 'token' => $this->request->data['User']['token'],],), true);
                                //$this->sendemail('signupemail', 'notification_email_layout', $this->request->data, $email_verification_link, 'Verification email');
                                /*
                                  Enter code here for case when email sending is failed.
                                 */
                                $this->Session->setFlash(__('Signup successfull.Please check your mailbox for verification mail(Dont forget to check SPAM also).'), 'flash_success');

                                return $this->redirect(array('action' => 'login'));
                            } else {
                                $this->Session->setFlash("Can't save data right now.Please try again later.", 'flash_error');
                            }
                        } else {
                            $email_verification_link = Router::url(array('controller' => 'users',
                                        'action' => 'verify',
                                        '?' => [
                                            'username' => $this->request->data['User']['username'], 'token' => $this->request->data['User']['token'],],), true);
                            //$this->sendemail('signupemail', 'notification_email_layout', $this->request->data, $email_verification_link, 'Verification email');
                            /*
                              Enter code here for case when email sending is failed.
                             */
                            $this->Session->setFlash(__('Signup successfull.Please check your mailbox for verification mail(Dont forget to check SPAM also).'), 'flash_success');

                            return $this->redirect(array('action' => 'login'));
                        }
                    } else {
                        $this->Session->setFlash("Can't save data right now.Please try again later.", 'flash_error');
                    }
                } else {
                    $this->Session->setFlash(__('Please check highlighted fields.'), 'flash_warning');
                    $this->set('pwdmatcherror', true);
                }
            } else {
                $this->Session->setFlash(__('Please check highlighted fields.'), 'flash_warning');
            }
        }
    }

    public function login() {
        if (AuthComponent::user('id')) {
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }

        $this->layout = 'mainlayout';

        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Session->setFlash(__('Please check entered details.'), 'flash_error');
            }
        }
    }

    public function signout() {
        /*
          Redirecting user to /users/index after logout.
         */
        if ($this->Auth->logout()) {
            $this->Session->setFlash(__('You have been succcessfully logged out.'), 'flash_success');

            return $this->redirect(array('controller' => 'users', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__('Cant logout.'), 'flash_error');
        }
    }

    /*
      Email verification after signup
     */

    public function verify() {
        /*
          Checking if get variables for token and username are present in url
         */
        if (isset($this->params['url']['token']) && isset($this->params['url']['username'])) {
            $token = $this->params['url']['token'];
            $username = $this->params['url']['username'];
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }

        /*
          Logging out any user currently loggedin so that no wrong data gets saved in our database.
         */
        if (AuthComponent::user('id')) {
            $this->Auth->logout();
        }

        /*
          Finding user if token and uesrname are present
         */
        $parameters = array(
            'conditions' => array(
                'username' => $username,
                'token' => $token,
            )
        );
        $userid = $this->User->find('first', $parameters);

        if ($userid) {
            /*
              Checking for the case when user is already verified
             */
            if ($userid['0']['User']['verified'] === Configure::read('user_verified')) {
                $this->Session->setFlash(__('You have been already verified.'), 'flash_warning');

                return $this->redirect(array('action' => 'index'));
            }

            /*
              Set id for User equal to id found from using token and email as we would be updating the verification
              status of the user.
             */
            $this->User->id = $userid['User']['id'];

            if (!$this->User->exists()) {
                throw new NotFoundException(__('Invalid URL'));
            }
            $this->request->data['User']['verified'] = Configure::read('user_verified');
            ;
            /*
              Changing token so that link sent to user is no longer valid
             */
            $this->request->data['User']['token'] = md5(rand());
            if ($this->User->save($this->request->data)) {

                /*
                  Sending welcome email to the user after user signs up.
                 */
                $this->sendemail('signup-welcome', 'notification_email_layout', $userid, '', 'Welcome to VerySure');
                $this->Session->setFlash(__('Congrats you have been verified.Now you can sigin to access our great site.'), 'flash_success');


                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                        __('You cant be veriied.Please try again.'), 'flash_error'
                );

                return $this->redirect(array('action' => 'index'));
            }
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }
    }

    /*
      This function is used for checking email address and sending change password email to user.
     */

    public function forgot() {
        $this->layout = 'mainlayout';

        if ($this->request->is('post')) {
            /*
              Finding user with the entered email
             */
            $parameters = array(
                'conditions' => array(
                    'username' => $this->request->data['User']['username'],
                ),
                'fields' => array('id', 'name'),
            );
            $userid = $this->User->find('first', $parameters);
            /*
              Saving email in another variable as $this->request->data['User']['username'] would be unset
              so that email doesnt gets updated in the database.
             */
            $email_entered = $this->request->data['User']['username'];
            if ($userid) {
                /*
                  Generating and saving new token
                 */
                $this->User->id = $userid['User']['id'];
                $forgot_token = str_shuffle(hash('sha512', (hash('sha256', $this->request->data['User']['username']
                                        . $userid['User']['name'])) . strval(time()) . md5(rand())));
                $this->request->data['User']['token'] = $forgot_token;
                /*
                  unset username so that it doesnt gets updated in database.
                 */
                unset($this->request->data['User']['username']);

                if ($this->User->save($this->request->data)) {
                    /*
                      Sending forgot password email
                     */
                    $forgot_email_verification_link = Router::url(array('controller' => 'users',
                                'action' => 'change_password',
                                '?' => [
                                    'username' => $email_entered, 'token' => $this->request->data['User']['token'],
                                    'forgot' => '1',],), true);
                    $forgot_email = new CakeEmail('mandrill_signup');
                    $forgot_email->to($email_entered);
                    $forgot_email->subject('Frogot Password');
                    $forgot_email->template('forgotemail', 'notification_email_layout')
                            ->viewVars(array('forgot_email_verification_link' => Router::url(array('controller' => 'users',
                                    'action' => 'change_password',
                                    '?' => [
                                        'username' => $email_entered, 'token' => $this->request->data['User']['token'], 'forgot' => '1',],), true),
                                'name_of_user' => $userid['User']['name'],));
                    $forgot_email->send();
                    /*
                      Enter code here for case when email sending is failed.
                     */
                    $this->Session->setFlash(__('Please check your mailbox.Also check SPAM.'), 'flash_success');
                } else {
                    $this->Session->setFlash(__('Please try again later.'), 'flash_error');
                }
            } else {
                $this->Session->setFlash(
                        __("Sorry we couldn't find any account related to that email.Please make sure you have entered
						correct email address."), 'flash_error'
                );
            }
        }
    }

    public function change_password() {
        $this->layout = 'mainlayout';

        if (isset($this->params['url']['token']) && isset($this->params['url']['username']) && isset($this->params['url']['forgot'])) {
            $forgot = $this->params['url']['forgot'];
            $token = $this->params['url']['token'];
            $username = $this->params['url']['username'];
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }

        /*
          Logging out any user currently loggedin so that no wrong data gets saved in our database.
         */
        if (AuthComponent::user('id')) {
            $this->Auth->logout();
        }

        if ($forgot === '1') {
            $parameters = array(
                'conditions' => array(
                    'username' => $username,
                    'token' => $token,
                ),
                'fields' => array('id', 'name'),
            );
            $userid = $this->User->find('first', $parameters);

            if (isset($userid)) {
                $this->User->id = $userid['User']['id'];

                if (!$this->User->exists()) {
                    throw new NotFoundException(__('Invalid URL'));
                }
                if ($this->request->is('post')) {
                    if ($this->User->validates()) {
                        if ($this->request->data['renter_password'] === $this->request->data['User']['password']) {
                            /*
                              Changing token so that change password link is no longer valid.
                             */
                            $this->request->data['User']['token'] = md5(rand());

                            if ($this->User->save($this->request->data)) {
                                $password_change_notification_email = new CakeEmail('mandrill_signup');
                                $password_change_notification_email->to($username);
                                $password_change_notification_email->subject('Password changed');
                                $password_change_notification_email->template('passowrd_changed_email', 'notification_email_layout')
                                        ->viewVars(array('name_of_user' => $userid['User']['name']));
                                $password_change_notification_email->send();

                                $this->Session->setFlash(__('Your password has been changed.
																						 Please login with your new password.'), 'flash_success');

                                return $this->redirect(array('action' => 'index'));
                            } else {
                                $this->Session->setFlash(__('Please check highlighted fields.'), 'flash_warning');
                            }
                        } else {
                            $this->Session->setFlash(__('Please check highlighted fields.'), 'flash_warning');
                            $this->set('pwdmatcherror', true);
                        }
                    } else {
                        $this->Session->setFlash(__('Please check highlighted fields.'), 'flash_warning');
                    }
                }
            } else {
                throw new NotFoundException(__('Invalid URL'));
            }
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }
    }

    /*
     * Send emails by getting messages from the SQS.
     */

    public function send_email() {
        $this->autorender = false;
        $this->layout = false;
        $aws_sdk = $this->get_aws_sdk();
        $sqs_client = $aws_sdk->createSqs();

        $email_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('email_queue')));
        $email_queue_localhost_url = $email_queue_localhost->get('QueueUrl');
        $this->log("send email running");
        $receive_email = $sqs_client->receiveMessage(array(
            'QueueUrl' => $email_queue_localhost_url,
            'MaxNumberOfMessages' => 5,
            'VisibilityTimeout' => 5
        ));
//        debug($receive_email['Messages']);
        while (count($receive_email) > 0) {
            foreach ($receive_email['Messages'] as $message) {
                $body = json_decode($message['Body']);
                $message_receipt_handle = $message['ReceiptHandle'];
                $userdata = array();
//                CakeLog::write('emails', $body);
                $this->log("email body");
                $this->log($body);
                $userdata['User']['name'] = $body->user_name;
                $userdata['User']['username'] = $body->user_username;
                if ($this->send_general_email($userdata, $body->link, $body->title, $body->content, $body->subject, $body->button_text)) {
                    $sqs_client->deleteMessage(array(
                        'QueueUrl' => $email_queue_localhost_url,
                        'ReceiptHandle' => $message_receipt_handle
                    ));
                }
            }
            $receive_email = $sqs_client->receiveMessage(array(
                'QueueUrl' => $email_queue_localhost_url,
                'MaxNumberOfMessages' => 5,
                'VisibilityTimeout' => 5
            ));
        }
    }

    public function upload_doc() {

        $this->autorender = false;
        $this->layout = false;
        $aws_sdk = $this->get_aws_sdk();
        $sqs_client = $aws_sdk->createSqs();
        $s3_client = $aws_sdk->createS3();

        $upload_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('upload_queue')));
        $upload_queue_localhost_url = $upload_queue_localhost->get('QueueUrl');
//        $this->log("send email running");
        $receive_upload = $sqs_client->receiveMessage(array(
            'QueueUrl' => $upload_queue_localhost_url,
            'MaxNumberOfMessages' => 1,
            'VisibilityTimeout' => 30
        ));
//        debug($receive_email['Messages']);
        while (count($receive_upload) > 0) {
            foreach ($receive_upload['Messages'] as $message) {
                $body = json_decode($message['Body']);
                $this->log('upload');
                $this->log($body);
                $message_receipt_handle = $message['ReceiptHandle'];

                $file_in_s3 = $s3_client->doesObjectExist(Configure::read('s3_bucket_name'), $body->docname);

                if ($file_in_s3) {
                    $sqs_client->deleteMessage(array(
                        'QueueUrl' => $upload_queue_localhost_url,
                        'ReceiptHandle' => $message_receipt_handle
                    ));
                    $this->log('already uploaded');
                } else {
                    $upload_result = $s3_client->upload(Configure::read('s3_bucket_name'), $body->docname, fopen(Configure::read('upload_location_url') . $body->docname, 'r'));
                    if ($upload_result) {
                        $sqs_client->deleteMessage(array(
                            'QueueUrl' => $upload_queue_localhost_url,
                            'ReceiptHandle' => $message_receipt_handle
                        ));
                        $this->log('uploaded');
                    }
                }
            }
            $receive_upload = $sqs_client->receiveMessage(array(
                'QueueUrl' => $upload_queue_localhost_url,
                'MaxNumberOfMessages' => 1,
                'VisibilityTimeout' => 30
            ));
        }
    }

}
