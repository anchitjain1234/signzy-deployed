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
            'conditions' => array('ownerid' => CakeSession::read('Auth.User.id'), 'status' => Configure::read('doc_rejected')),
            'limit' => 5,
            'order' => array('created' => -1),
        );
        $this->set('disputeduploads', $this->Document->find('all', $params));
        
        $profileinfo = $this->Profile->find('first', array('conditions' => array('uid' => CakeSession::read('Auth.User.id'))));
//        
//        if(isset($profileinfo) && $profileinfo!=[] && ($profileinfo['Profile']['verified'] != Configure::read('profile_verified') || $profileinfo['Profile']['verified'] != Configure::read('profile_unverified')))
//        {
//            $this->Session->setFlash('Your account is not yet verified. Please verify by uploading your pan card','flash_error');
//        }
//        else
//        {
//            $this->Session->setFlash('Your account is not yet verified. Please verify by uploading your pan card','flash_error');
//        }
        $this->set('profile',$profileinfo);
    }

    public function verification() {
        if ($this->request->is('get')) {
            $previous_existence_check = $this->Profile->find('first', array('conditions' => array('uid' => CakeSession::read('Auth.User.id'))));
            if ($previous_existence_check) {
                if(!isset($previous_existence_check['Profile']['profilepicture']))
                {
                    $this->Session->setFlash('Please capture your profile picture first having your face before applying for verification.', 'flash_error');
                    return $this->redirect(array('controller' => 'profile', 'action' => 'index'));
                }
                if ($previous_existence_check['Profile']['verified'] === Configure::read('profile_rejected')) {
                    $this->render();
                } elseif ($previous_existence_check['Profile']['verified'] === Configure::read('profile_unverified')) {
                    /*
                     * If user application is in provisioning.
                     */
                    $this->Session->setFlash('Your profile is in verification process.Pleasae wait for it to finish.', 'flash_error');
                    return $this->redirect(array('controller' => 'profile', 'action' => 'index'));
                } elseif ($previous_existence_check['Profile']['verified'] === Configure::read('profile_verified')) {
                    /*
                     * If user application has been verified before.
                     */
                    $this->Session->setFlash('Your profile is already verified.', 'flash_error');
                    return $this->redirect(array('controller' => 'profile', 'action' => 'index'));
                }
            }
            else
            {
                $this->Session->setFlash('Please capture your profile picture first having your face before applying for verification.', 'flash_error');
                return $this->redirect(array('controller' => 'profile', 'action' => 'index'));
            }
        }
    }

    public function profilepicture() {
        if($this->request->is('post')){
            $profile = $this->request->data['profile'];
            $profile_check = $this->Profile->find('first',array('conditions'=>array('uid'=>CakeSession::read('Auth.User.id'))));
            
            /*
             * Save base64 encoded image into location.
             */
            
            /*
             * Add code here to upload this asynchronusly into S3.
             */
            list($type, $data) = explode(';', $profile);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);

            $imgname = $this->get_temporary_document_name();
            $complete_path = Configure::read('image_upload_location') . $imgname . ".png";
            $file_created = fopen($complete_path, 'w');
            file_put_contents($complete_path, $data);

            if(isset($profile_check) && $profile_check!=[])
            {
                $this->Profile->id=$profile_check['Profile']['id'];
                $this->Profile->set('profilepicture',$imgname.".png");
                
                if($this->Profile->save())
                {
                    $this->Session->setFlash('Profile Picture updated successfully.','flash_success');
                    echo '{"success":true}';
                    exit();
                }
                echo '{"error":1}';
                exit();
            }
            else
            {
                /*
                 * Case when profile of user does not exists.
                 */
                $this->Profile->create();
                $this->Profile->set('uid',CakeSession::read('Auth.User.id'));
                $this->Profile->set('profilepicture',$imgname.".png");
                if($this->Profile->save())
                {
                    $this->Session->setFlash('Profile Picture updated successfully.','flash_success');
                    echo '{"success":true}';
                    exit();
                }
                echo '{"error":1}';
                exit();
            }
        }
    }

    public function imgupload() {
        $this->request->allowMethod('ajax');
        $this->autorender = false;
        $this->layout = false;
//        print_r($_FILES);

        $allowed = array('jpg', 'png', 'jpeg');
        if (isset($_FILES['data']['name']['Profile']['file']) && $_FILES['data']['error']['Profile']['file'] == 0) {
            $extension = pathinfo($_FILES['data']['name']['Profile']['file'], PATHINFO_EXTENSION);

            if (!in_array(strtolower($extension), $allowed)) {
                echo '{"documentstatus" : false , "error": -1}';
                exit;
            }

            $temporary_document_name = $this->get_temporary_document_name();
            $temporary_document_name = $temporary_document_name . "." . $extension;

            if (move_uploaded_file($_FILES['data']['tmp_name']['Profile']['file'], Configure::read('image_upload_location') . $temporary_document_name)) {

                $aws_sdk = $this->get_aws_sdk();
//                    $s3_client = $aws_sdk->createS3();
                $sqs_client = $aws_sdk->createSqs();

                $uploading_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('upload_queue')));
                $uploading_queue_localhost_url = $uploading_queue_localhost->get('QueueUrl');

                $sqs_client->setQueueAttributes(array(
                    'QueueUrl' => $uploading_queue_localhost_url,
                    'Attributes' => array(
                        'VisibilityTimeout' => 5,
                        'ReceiveMessageWaitTimeSeconds' => 5,
                        'DelaySeconds' => 0
                    ),
                ));

//                    $this->add_upload_message_sqs($temporary_document_name, $sqs_client, $uploading_queue_localhost_url);
//                    $this->log('going to upload into s3');
//                    $this->upload_s3_from_sqs();
//                    $this->log('exiting from s3');

                echo '{"documentstatus" : true '
                . ',"documentname" : "' . $temporary_document_name . '"'
                . ',"documentsize":' . $_FILES['data']['size']['Profile']['file'] . ''
                . ',"documentoriginalname":"' . $_FILES['data']['name']['Profile']['file'] . '"'
                . ',"documenttype":"' . $_FILES['data']['type']['Profile']['file'] . '"}';
                exit;
            }
            /*
             * If document has been uploaded to server but cannot be moved to safe location.
             */
            echo '{"documentstatus" : false , "error":' . $_FILES['data']['error']['Document']['file'] . '}';
        }
        /*
         * Case when document could not be uploaded completely on our server which could be due to size limitations or some
         * other problem.
         */
        echo '{"documentstatus" : false }';
    }

    /*
     * Add code here in preview retrive images from s3 bucket if not present locally in server.
     */

    public function preview($imgname = null) {
        $this->layout = false;

        if (!$imgname) {
            throw new NotFoundException(__('Invalid Image'));
        }

        $imgurl = Configure::read('image_upload_location') . $imgname;
        $file_check = file_exists($imgurl);

        if ($file_check) {
            $ntct = Array("1" => "image/gif",
                "2" => "image/jpeg",
                "3" => "image/png",
                "6" => "image/bmp",
                "17" => "image/ico");
            header('Content-type: ' . $ntct[exif_imagetype($imgurl)]);
//            $extension = strtolower(pathinfo($imgname, PATHINFO_EXTENSION));
//            
//            switch ($extension) {
//                case "gif": $ctype = "image/gif";
//                    break;
//                case "png": $ctype = "image/png";
//                    break;
//                case "jpeg":
//                case "jpg": $ctype = "image/jpeg";
//                    break;
//                default:
//            }
//            header('Content-type: ' . $ctype);
//            header("Content-Length: " . filesize($imgurl));
            readfile($imgurl);
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }
    }

    public function facefromcard() {
        $this->request->allowMethod('ajax');
        $this->autorender = false;
        $this->layout = false;

        if ($this->request->is('post')) {
            $imgname = $this->request->data['imgname'];
//            $command = "python " . Configure::read('python_scripts_location') . "test.py " . Configure::read('image_upload_location') . $imgname;
//            $output = array();
//            $res = system($command, $output);
//            echo $res;
            echo "success";
        }
    }

    public function submitverification() {
        $this->request->allowMethod('ajax');
        $this->autorender = false;
        $this->layout = false;

        if ($this->request->is('post')) {
            $imgname = $this->request->data['name'];

            /*
             * Checking if the user has applied before for profile verification and has been rejected
             */

            $previous_existence_check = $this->Profile->find('first', array('conditions' => array('uid' => CakeSession::read('Auth.User.id'))));
            if ($previous_existence_check) {
                if ($previous_existence_check['Profile']['verified'] === Configure::read('profile_rejected') || $previous_existence_check['Profile']['verified'] === Configure::read('profile_not_exists')) {
                    $this->Profile->id = $previous_existence_check['Profile']['id'];
                    $this->Profile->set('verified', Configure::read('profile_unverified'));
                    $this->Profile->set('cardnumber', $this->request->data['cardnumber']);
                    $this->Profile->set('verificationimage', $imgname);
                } elseif ($previous_existence_check['Profile']['verified'] === Configure::read('profile_unverified')) {
                    /*
                     * If user application is in provisioning.
                     */
                    $this->Session->setFlash('Your profile is in verification process.Pleasae wait for it to finish.', 'flash_error');
                    return $this->redirect(array('controller' => 'profile', 'action' => 'index'));
                } elseif ($previous_existence_check['Profile']['verified'] === Configure::read('profile_verified')) {
                    /*
                     * If user application has been verified before.
                     */
                    $this->Session->setFlash('Your profile is already verified.', 'flash_error');
                    return $this->redirect(array('controller' => 'profile', 'action' => 'index'));
                }
            } else {
                /*
                 * If user is applying for first time.
                 */
                $this->Profile->create();
                $this->Profile->set('uid', CakeSession::read('Auth.User.id'));
                $this->Profile->set('verified', Configure::read('profile_unverified'));
                $this->Profile->set('verificationimage', $imgname);
                $this->Profile->set('cardnumber', $this->request->data['cardnumber']);
            }
            if ($this->Profile->save()) {
                /*
                 * Add code here to send emails to support staff so that they can verify the profile.
                 */
                $aws_sdk = $this->get_aws_sdk();
                $sqs_client = $aws_sdk->createSqs();

                $email_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('email_queue')));
                $email_queue_localhost_url = $email_queue_localhost->get('QueueUrl');

                $email_to_be_sent = array();
                $email_to_be_sent['link'] = Router::url(array('controller' => 'profile', 'action' => 'verifyuserprofile', '?' => ["uid" => CakeSession::read('Auth.User.id')]), true);
                $email_to_be_sent['title'] = 'Profile Approval';
                $email_to_be_sent['subject'] = 'Approval of Profile';
                $email_to_be_sent['content'] = 'Please click below link to verify the user profile.';
                $email_to_be_sent['button_text'] = 'Verify Profile';

                $this->loadModel('User');
                $support_staff = $this->User->find('all', array('conditions' => array('type' => Configure::read('support'))));

                foreach ($support_staff as $support) {
                    $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $support);
                }

                echo '{"success":true}';
                $this->send_email_from_sqs();
                exit();
            }
            echo '{"error":1}';
            exit();
        }
        echo '{"error":2}';
        exit();
    }

    public function verifyuserprofile() {
        $this->loadModel('User');
        $userinfo = $this->User->find('first', array('conditions' => array('id' => CakeSession::read('Auth.User.id'))));

        if ($this->request->is('get')) {

            if (isset($userinfo['User']['type']) && $userinfo['User']['type'] != '' && $userinfo['User']['type'] != Configure::read('customer')) {

                if (isset($this->params['url']['uid'])) {
                    $applier_id = $this->params['url']['uid'];
                } else {
                    throw new NotFoundException('Invalid URL format');
                }
                $applier_profile = $this->Profile->find('first', array('conditions' => array('uid' => $applier_id)));

                if (!$applier_profile) {
                    throw new NotFoundException('User not having profile');
                }

                $this->set('userinfo', $applier_profile);
            }
            else
            {
                throw new NotFoundException('Invalid URL');
            }
        }

        if ($this->request->is('post') && isset($userinfo['User']['type']) && $userinfo['User']['type'] != '' && $userinfo['User']['type'] != Configure::read('customer')) {
            $this->request->allowMethod('ajax');
            $this->autorender = false;
            $this->layout = false;

            $option = $this->request->data['option'];
            $uid = $this->request->data['uid'];
            $pid = $this->request->data['pid'];

            $aws_sdk = $this->get_aws_sdk();
            $sqs_client = $aws_sdk->createSqs();

            $email_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('email_queue')));
            $email_queue_localhost_url = $email_queue_localhost->get('QueueUrl');

            $this->loadModel('User');
            $userdata = $this->User->find('first', array('conditions' => array('id' => $uid)));

            if ($option == 'TRUE') {
                $this->Profile->id = $pid;
                $this->Profile->set('verified', Configure::read('profile_verified'));

                if ($this->Profile->save()) {
                    /*
                     * Add code here to send email to the user that he has been verified.
                     */

                    $email_to_be_sent = array();
                    $email_to_be_sent['link'] = Router::url(array('controller' => 'dashboard', 'action' => 'index'), true);
                    $email_to_be_sent['title'] = 'Profile Verified';
                    $email_to_be_sent['subject'] = 'Profile Verified';
                    $email_to_be_sent['content'] = 'Congrats your profile has been verified.You can now sign documents.'
                            . 'PLease click below button to visit your dashboard.';
                    $email_to_be_sent['button_text'] = 'Dashboard';
                    $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $userdata);

                    echo '{"success":1}';
                    $this->send_email_from_sqs();
                    exit();
                }
                echo '{"error":1}';
                exit();
            } else {
                $this->Profile->id = $pid;
                $this->Profile->set('verified', Configure::read('profile_rejected'));

                if ($this->Profile->save()) {
                    /*
                     * Add code here to send email to the user that his verification has been rejected.However he can reapply again.
                     */

                    $email_to_be_sent = array();
                    $email_to_be_sent['link'] = Router::url(array('controller' => 'profile', 'action' => 'index'), true);
                    $email_to_be_sent['title'] = 'Profile Rejected';
                    $email_to_be_sent['subject'] = 'Profile Rejected';
                    $email_to_be_sent['content'] = 'Sorry we cant verify the details you provided but you can reapply again.'
                            . 'PLease click below button to reapply for verification.';
                    $email_to_be_sent['button_text'] = 'Your Profile';
                    $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $userdata);

                    echo '{"success":2}';
                    $this->send_email_from_sqs();
                    exit();
                }
            }
        }
    }
    
    public function support()
    {
        $userinfo = $this->User->find('first', array('conditions' => array('id' => CakeSession::read('Auth.User.id'))));
        
        if (isset($userinfo['User']['type']) && $userinfo['User']['type'] != '' && $userinfo['User']['type'] != Configure::read('customer')) {
            
            if($this->request->is('get'))
            {
                $unverifiedprofiles = $this->Profile->find('all',array('conditions'=>array('verified'=>  Configure::read('profile_unverified'))));
                $this->loadModel('Col');
                $unverifiedphotos = $this->Col->find('all',array('conditions'=>array('photoverification'=>  Configure::read('photo_unverified'))));
                $requests_with_time_key = array();
                foreach($unverifiedprofiles as $profile)
                {
                    $link = array();
                    $link['type']='Profile Verification';
                    $link['time']=$profile['Profile']['modified'];
                    $link['link']  = Router::url(array('controller' => 'profile', 'action' => 'verifyuserprofile','?' => ["uid" => $profile['Profile']['uid']]), true);
                    $requests_with_time_key[$profile['Profile']['modified']->sec] = $link;
                }
                foreach($unverifiedphotos as $photo)
                {
                    $link = array();
                    $link['type']='Signatory Photo Verification';
                    $link['time']=$photo['Col']['modified'];
                    $link['link']  = Router::url(array('controller' => 'documents', 'action' => 'photoverification', '?' => ["did" => $photo['Col']['did'], "uid" => $photo['Col']['uid']]), true);
                    $requests_with_time_key[$photo['Col']['modified']->sec] = $link;
                }
                krsort($requests_with_time_key);
//                debug($requests_with_time_key);
                $this->set('requests',$requests_with_time_key);
            }
        }
        else
        {
            throw new NotFoundException('Invalid URL');
        }
    }
    
    public function admin()
    {
        $userinfo = $this->User->find('first', array('conditions' => array('id' => CakeSession::read('Auth.User.id'))));
        
        if (isset($userinfo['User']['type']) && $userinfo['User']['type'] != '' && $userinfo['User']['type'] == Configure::read('admin')) {
            
            if($this->request->is('get')){
                $this->render();
            }
            if($this->request->is('post'))
            {
                $this->request->allowMethod('ajax');
                $this->autorender = false;
                $this->layout = false;
                
                $email_entered = $this->request->data['email'];
                $type = $this->request->data['type'];
                
                $aws_sdk = $this->get_aws_sdk();
                $sqs_client = $aws_sdk->createSqs();

                $email_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('email_queue')));
                $email_queue_localhost_url = $email_queue_localhost->get('QueueUrl');
                
                $this->loadModel('User');
                $user = $this->User->find('first',array('conditions'=>array('username'=>$email_entered)));
                
                $this->User->id = $user['User']['id'];
                $this->User->set('type', $type);
                
                if($this->User->save())
                {
                    $email_to_be_sent = array();
                    $email_to_be_sent['link'] = Router::url(array('controller' => 'profile', 'action' => 'index'), true);
                    $email_to_be_sent['title'] = 'Support Acoount';
                    $email_to_be_sent['subject'] = 'Support Acoount';
                    $email_to_be_sent['content'] = 'Congrats your account has been eleveated to become support account.'
                            . 'You can now review the verification requests.Click below button to view tour profile and review'
                            . 'verification requests.';
                    $email_to_be_sent['button_text'] = 'Your Profile';
                    $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $user);
                    echo '{"success":true}';
                    $this->send_email_from_sqs();
                    exit();
                }
                echo '{"error":1}';
                exit();
            }
        }
        else
        {
            throw new NotFoundException('Invalid URL');
        }
    }

}
