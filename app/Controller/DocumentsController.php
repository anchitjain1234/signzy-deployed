<?php

require('../vendor/PDFMerger/PDFMerger.php');

class PDF extends FPDF {

    function Header() {
        // Logo
        $this->Image('/home/ubuntu/logo.png', 10, 6, 30);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(70, 10, 'Signatories Included', 1, 0, 'C');
        // Line break
//                $this->Ln(20);
    }

}

App::uses('CakeEmail', 'Network/Email');
App::uses('Validation', 'Utility');

class DocumentsController extends AppController {

    public $uses = array('Document');
    public $helpers = array('Form', 'UploadPack.Upload');
    public $layout = 'insidelayout';
    public $components = array('RequestHandler');

    public function beforeFilter() {
        /*
          To give the name ofthe user to the layout befire the view is loaded.
         */

        $this->loadModel('User');
        $uid = CakeSession::read('Auth.User.id');
        $params = array(
            'fields' => array('name', 'username'),
            'conditions' => array('id' => $uid),
        );
        $userdata = $this->User->find('first', $params);
        $this->set('name', $userdata['User']['name']);
        $this->set('useremail', $userdata['User']['username']);
        $this->set('userid', $uid);
    }

    /*
     * It shows all the documents in which current logged in user is involved.
     */

    public function index() {
        /*
          To display all the previous uploaded documents of the user as well as the documents in which the
          user id requested to sign it i.e.
         * documents of which user is owner as well as docs in which he is collabarator.
         */

        /*
         * $docs_with_timeaskey will constain docinfo with its modified time as key
         * value.
         */

        $docs_with_timeaskey = array();

        $uid = CakeSession::read('Auth.User.id');

        $parameters = array(
            'conditions' => array('ownerid' => $uid),
        );
        $user_documents_data = $this->Document->find('all', $parameters);

        foreach ($user_documents_data as $doc):
            $docs_with_timeaskey[$doc['Document']['modified']->sec] = $doc;
        endforeach;

        $user_documents_data = array();

        /*
          Now finding documents in which user is a collabarator
         */

        $this->loadModel('Col');

        $parameters = array(
            'conditions' => array('uid' => $uid)
        );
        $coldata = $this->Col->find('all', $parameters);

        $this->loadModel('Compmember');
        if ($coldata) {
            foreach ($coldata as $col):
                if (isset($col['Col']['cid'])) {
                    $status = array();
                    array_push($status, array('status' => Configure::read('legal_head')));
                    array_push($status, array('status' => Configure::read('auth_sign')));
                    $authorized_check = $this->Compmember->find('count', array('conditions' => array('cid' => $col['Col']['cid'], 'uid' => $col['Col']['uid'], '$or' => $status)));
                    if ($authorized_check != 0) {
                        $parameters = array(
                            'conditions' => array('id' => $col['Col']['did']),
                        );

                        $doc_data = $this->Document->find('first', $parameters);
                        $docs_with_timeaskey[$doc_data['Document']['modified']->sec] = $doc_data;
                        array_push($user_documents_data, $doc_data);
                    }
                } else {
                    $parameters = array(
                        'conditions' => array('id' => $col['Col']['did']),
                    );

                    $doc_data = $this->Document->find('first', $parameters);
                    $docs_with_timeaskey[$doc_data['Document']['modified']->sec] = $doc_data;
                    array_push($user_documents_data, $doc_data);
                }
            endforeach;
        }
        krsort($docs_with_timeaskey);
        $this->set('user_documents_data', $docs_with_timeaskey);
    }

    /*
     * It will save the document in document and col database.
     * The data will be provided from upload_ajax
     */

    public function upload() {
        $flag_for_not_sending_email_to_legal_head = 0;
        
        if($this->request->is('get'))
        {
            $this->loadModel('Profile');
            $profiledata = $this->Profile->find('first',array('conditions'=>array('uid'=>CakeSession::read('Auth.User.id'))));
            if(!isset($profiledata) || $profiledata === [])
            {
                $this->Session->setFlash('You cant upload new document as your profile has not been verified or is in process of verification.', 'flash_error');
                return $this->redirect(array('controller'=>'dashboard','action'=>'index'));
            }
            else if(isset($profiledata) && $profiledata['Profile']['verified']!= Configure::read('profile_verified'))
            {
                $this->Session->setFlash('You cant upload new document as your profile has not been verified or is in process of verification.', 'flash_error');
                return $this->redirect(array('controller'=>'dashboard','action'=>'index'));
            }
        }
        
        if ($this->request->is('post')) {

            $this->request->allowMethod('ajax');
            $this->autorender = false;
            $this->layout = false;

            if (isset($this->request->data['emails'])) {
                $emails = json_decode($this->request->data['emails']);
            }

            if (isset($this->request->data['company_info'])) {
                $companies_info = $this->request->data['company_info'];
            }

            if (isset($this->request->data['biometric_info'])) {
                $biometric_info = json_decode($this->request->data['biometric_info']);
            }


            $current_name = $this->request->data['doc_name'];

            /*
             * If user does not specifies the document name ,we would give name of the document as its original name.
             */

            if (isset($this->request->data['name']) && $this->request->data['name'] != "") {
                $change_to_name = $this->request->data['name'];
            } else {
                $change_to_name = $this->request->data['doc_org_name'];
            }

            $original_name = $this->request->data['doc_org_name'];
            $size = $this->request->data['doc_size'];
            $type = $this->request->data['doc_type'];

            $owner_data = $this->User->find('first', array('conditions' => array('id' => CakeSession::read('Auth.User.id'))));
            /*
             * Checking if file with name provided exists in location or not.
             */

            if (file_exists(Configure::read('upload_location_url') . $current_name)) {

                $document_check_in_db = $this->Document->find('count', array('conditions' => array('originalname' => $current_name)));

                if ($document_check_in_db === 0) {

                    /*
                     * Saving document data . In document table "originalname" refers to the name
                     * by ehich file is stored in the storage area
                     * and "name" refers to the name given by user.
                     */
                    $this->Document->create();
                    $this->Document->set('name', $change_to_name);
                    $this->Document->set('size', $size);
                    $this->Document->set('type', $type);
                    $this->Document->set('originalname', $current_name);
                    $this->Document->set('ownerid', CakeSession::read("Auth.User.id"));
                    $this->Document->set('status', "0");

                    if ($this->Document->save()) {

                        /*
                         * Getting the document data.
                         */
                        $docid = $this->Document->find('first', array('conditions' => array('originalname' => $current_name)));

//                        /*
//                         * Also saving the document owner details in the col table with a different status.
//                         * This will help in checking document related to user details fast as we would not 
//                         * have to lookup in both documents and cols table , just we have to look in 
//                         * cols table.
//                         */
                        $this->loadModel('Col');
//                        $this->Col->create();
//                        $this->Col->set('did', $docid['Document']['id']);
//                        $this->Col->set('status', Configure::read('doc_owner'));
//                        $this->Col->set('photoverification', Configure::read('photo_not_exists'));
//                        $this->Col->set('uid', CakeSession::read('Auth.User.id'));
//                        $this->Col->save();

                        /*
                         * Checking if any signatories are there or not.
                         */

                        if (count($emails) > 0) {

                            /*
                             * Checking if any of the emails goven in signatory is invalid or not.
                             * If anyone is invalid it does means that user is tampering so we should put him in risky category.
                             */

                            foreach ($emails as $email):
                                if (!Validation::email($email)) {
                                    throw new NotFoundException(__('Error while saving data.'));
                                }
                            endforeach;

                            $this->loadModel('Company');
                            $this->loadModel('Compmember');

                            /*
                             * $companies will have the name of companies that the user has requested to be a collabarator.
                             * 
                             * $company_info_from_db will contain the information of company fetched from company tables. It will store
                             * data in form of key value pair . Key would be company name and value would be company info fetched.
                             * 
                             * $company_member_info_from_db will contain the information of all the signatories of the companies.
                             * Information will be stored as key value. Key = Comoany Name , Value = Signatories.
                             *  
                             */
                            $companies = array();
                            $company_info_from_db = array();
                            $company_member_info_from_db = array();

                            $status = array();
                            array_push($status, array('status' => Configure::read('legal_head')));
                            array_push($status, array('status' => Configure::read('auth_sign')));

                            if (isset($companies_info)) {
                                foreach ($companies_info as $email => $company):
                                    if (!in_array($company, $companies)) {
                                        $comp_info = $this->Company->find('first', array('conditions' => array('name' => $company)));
                                        if (isset($comp_info) && $comp_info != '') {
                                            array_push($companies, $company);
                                            $company_info_from_db[$company] = $comp_info;
                                            $company_member_info_from_db[$company] = $this->Compmember->find('all', array('conditions' => array('cid' => $comp_info['Company']['id'], '$or' => $status)));
                                        } else {
                                            throw new NotFoundException(__('Error while saving data.'));
                                        }
                                    }
                                endforeach;
                            }


                            $this->log($company_info_from_db);
                            $this->log($company_member_info_from_db);
                            $this->loadModel('User');


                            foreach ($emails as $email):
                                /*
                                 * Flags for distinguishing when user is not signing on any companys behalf 
                                 * and when user is signing on some comopany behalf than he is authorised or not.
                                 */
                                $flag_for_not_sending_email_to_legal_head = 0;
                                $flag_when_company_is_not_added = 1;

                                /*
                                 * Finding user related to the email.
                                 */

                                $userdata = $this->User->find('first', array('conditions' => array('username' => $email)));

                                /*
                                 * Checking if collabarator is requested to sign on some company's behalf.
                                 */
                                if (isset($companies_info[$email]) && $companies_info[$email] !== '') {

                                    $flag_for_not_sending_email_to_legal_head = 0;
                                    $flag_when_company_is_not_added = 0;
                                    $comp_members = $company_member_info_from_db[$companies_info[$email]];

                                    /*
                                     * Checking for the condition when email is found in company members list and that email
                                     * is authorised signatory or legal head.
                                     */
                                    foreach ($comp_members as $comp_member) {

                                        /*
                                         * If current user is authorised signatory or legal head.
                                         */
                                        if ($comp_member['Compmember']['uid'] === $userdata['User']['id'] &&
                                                ($comp_member['Compmember']['status'] === Configure::read('auth_sign') || $comp_member['Compmember']['status'] === Configure::read('legal_head'))) {
                                            $flag_for_not_sending_email_to_legal_head = 1;
                                            break;
                                        }
                                    }
                                }

                                $aws_sdk = $this->get_aws_sdk();
                                $sqs_client = $aws_sdk->createSqs();

                                $email_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('email_queue')));
                                $email_queue_localhost_url = $email_queue_localhost->get('QueueUrl');

                                $sqs_client->setQueueAttributes(array(
                                    'QueueUrl' => $email_queue_localhost_url,
                                    'Attributes' => array(
                                        'VisibilityTimeout' => 5,
                                        'ReceiveMessageWaitTimeSeconds' => 5,
                                        'DelaySeconds' => 0
                                    ),
                                ));

                                /*
                                 * Add this unauthorised user as unauthorised signatory under company members table if he is not present 
                                 * as unauthorised user before. 
                                 */

                                if ($flag_when_company_is_not_added === 0 && $flag_for_not_sending_email_to_legal_head == 0) {
                                    $previously_present_check = $this->Compmember->find('first', array('conditions' => array('cid' => $company_info_from_db[$companies_info[$email]]['Company']['id'], 'uid' => $userdata['User']['id'])));
//                                    $this->log('Entering in flags.');
//                                    $this->log('$previously_present_check');
//                                    $this->log($previously_present_check);
                                    /*
                                     * If this collabarator is already present as rejected signatory in Compmember than send the email to the 
                                     * document owner that this signatory is already rejected from the legal heads of the company.
                                     */
                                    if (count($previously_present_check) != 0 && ($previously_present_check['Compmember']['status'] === Configure::read('rejected_sign'))) {
                                        $email_to_be_sent = array();
                                        $email_to_be_sent['link'] = Router::url(array('controller' => 'documents', 'action' => 'edit', $company_info_from_db[$companies_info[$email]]['Company']['id']), true);
                                        $email_to_be_sent['title'] = 'Signatory rejected';
                                        $email_to_be_sent['subject'] = $userdata['User']['name'] . ' has been rejected from signining on ' . $company_info_from_db[$companies_info[$email]]['Company']['name'] . '\'s behalf';
                                        $email_to_be_sent['content'] = 'Unfortunately ' . $userdata['User']['name'] . 'whom you added as an authorized signatory'
                                                . 'for ' . $docid['Document']['name'] . ' on behalf of ' . $company_info_from_db[$companies_info[$email]]['Company']['name']
                                                . ' has been rejected by the company legal head(s).Please click the below button to edit your signatories and remove'
                                                . 'the rejected signatories.';
                                        $email_to_be_sent['button_text'] = 'Edit signatories';
                                        $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $userdata);
                                        continue;
                                    }
                                    /*
                                     * If nothing is found linked with this company and user than add add the user as
                                     * unauthorized signatory.
                                     */ elseif (count($previously_present_check) === 0) {
                                        $this->Compmember->create();
                                        $this->Compmember->set('cid', $company_info_from_db[$companies_info[$email]]['Company']['id']);
                                        $this->Compmember->set('uid', $userdata['User']['id']);
                                        $this->Compmember->set('status', Configure::read('unauth_sign'));
                                        $this->Compmember->save();
                                    }
                                }

                                /*
                                 * Saving corresponding data in cols table.
                                 */

                                $this->Col->create();
                                $this->Col->set('did', $docid['Document']['id']);

                                /*
                                  Change status here to default status
                                 */
                                $this->Col->set('status', Configure::read('doc_pending'));
                                $token = $this->generate_token($email, $userdata['User']['name']);
                                $this->Col->set('token', $token);
                                $this->Col->set('uid', $userdata['User']['id']);
                                $this->Col->set('photoverification', Configure::read('photo_not_exists'));
                                /*
                                 * Saving the company id in collabaratotrs when authorised signatory is on behalf of some company.
                                 */
                                if ($flag_when_company_is_not_added === 0) {
                                    $this->Col->set('cid', $company_info_from_db[$companies_info[$email]]['Company']['id']);
                                }

                                if ($this->Col->save()) {
                                    if ($flag_for_not_sending_email_to_legal_head === 1 || $flag_when_company_is_not_added === 1) {
                                        /*
                                          Send email to the saved collabarator
                                         */

                                        $document_signing_link = Router::url(array('controller' => 'documents',
                                                    'action' => 'sign',
                                                    "?" => [
                                                        "userid" => $userdata['User']['id']
                                                        , "token" => $token
                                                        , "docuid" => $docid['Document']['id']])
                                                        , true);

                                        $email_to_be_sent = array();
                                        $email_to_be_sent['link'] = $document_signing_link;
                                        $email_to_be_sent['title'] = 'Document SIgning Request';
                                        $email_to_be_sent['subject'] = 'Document Signing Request';
                                        $email_to_be_sent['content'] = 'You have been requested by ' . $owner_data['User']['name'] . ' to sign a document on our website.Please '
                                                . 'click below button to view your document signing requests.';
                                        $email_to_be_sent['button_text'] = 'View Document Signing Request';
                                        $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $userdata);
                                    } else {
                                        /*
                                         * Sending email to legal head(s) to ask permission for this collabarator to sign the document.
                                         * Add code here to send email to legal head only once.
                                         */
                                        $comp_member_info = $company_member_info_from_db[$companies_info[$email]];

                                        foreach ($comp_member_info as $comp_member):
                                            if ($comp_member['Compmember']['status'] === Configure::read('legal_head')) {
                                                $user_info = $this->User->find('first', array('conditions' => array('id' => $comp_member['Compmember']['uid'])));
                                                $email_to_be_sent = array();
                                                $email_to_be_sent['link'] = Router::url(array('controller' => 'compmember', 'action' => 'authorise_user', $company_info_from_db[$companies_info[$email]]['Company']['id']), true);
                                                $email_to_be_sent['title'] = 'Permission for Signing';
                                                $email_to_be_sent['subject'] = 'Authorising ' . $userdata['User']['name'] . ' for document signing';
                                                $email_to_be_sent['content'] = $userdata['User']['name'] . " has requested to sign on" . $company_info_from_db[$companies_info[$email]]['Company']['name'] . ".Click on below button to authorise "
                                                        . "user for signing the document.";
                                                $email_to_be_sent['button_text'] = 'Authorize user';
                                                $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $user_info);
                                            }
                                        endforeach;

                                        /*
                                         * Sending mail to the collabarator with the signing link which would only be valid if he becomes 
                                         * authorized signatory in future.
                                         */
//                                        $title = 'Document Signing Request';
                                        $link = Router::url(array('controller' => 'documents',
                                                    'action' => 'sign',
                                                    "?" => [
                                                        "userid" => $userdata['User']['id']
                                                        , "token" => $token
                                                        , "docuid" => $docid['Document']['id']])
                                                        , true);

                                        $email_to_be_sent = array();
                                        $email_to_be_sent['link'] = $link;
                                        $email_to_be_sent['title'] = 'Document Signing Request';
                                        $email_to_be_sent['subject'] = 'Authoirzed Signatory for ' . $company_info_from_db[$companies_info[$email]]['Company']['name'];
                                        $email_to_be_sent['content'] = $owner_data['User']['name'] . " has requested for you to sign on " . $company_info_from_db[$companies_info[$email]]['Company']['name'] . " behalf.Wait for your authorisation from that "
                                                . "company.Click below button to send email again to company legal head for granting access.";
                                        $email_to_be_sent['button_text'] = "Sign Document";
                                        $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $userdata);
                                    }
                                } else {
                                    echo '{"finaldocstatus":false,"error":2}';
                                }

                            endforeach;
                            $this->log('Call going to sqs');
                            /*
                             * Send async call to send_email to send emails prtesent in queue.
                             */
                            $this->send_email_from_sqs();
//                            exec("wget -qO- http://localhost/cakephp/users/send_email  > /dev/null 2>/dev/null &");
                            $this->log('Call exited from sqs');
                            echo '{"finaldocstatus":true}';
                            $this->Session->setFlash(__('Document uploaded suceessfully and mails sent to all signatories'), 'flash_success');
                            exit;
                        }
                        /*
                         * Case when no signatories are present.
                         */
                        echo '{"finaldocstatus":false,"error":1}';
                        exit;
                    }
                    /*
                     * Case when document data can't be saved into the table.
                     */
                    echo '{"finaldocstatus":false,"error":2}';
                    exit;
                }
                /*
                 * Case when document already exists in database.
                 */
                echo '{"finaldocstatus":false,"error":2}';
                exit;
            }
            /*
             * Case when document is not present in saved location.
             */
            echo '{"finaldocstatus":false,"error":2}';
            exit;
        }
    }

    /*
     * It will move the uploaded file in saving location and will return the data
     * which would be sent to upload method.
     */

    public function upload_ajax() {
        $this->request->allowMethod('ajax');
        $this->autorender = false;
        $this->layout = false;

        /*
         * Getting headers of the request to check the url referer
         * Referer should be /documents/
         * upload
         */
        $headers = getallheaders();

        /*
         * Checking if url referrer matches
         */
        if ($headers['Referer'] === Router::url(array('controller' => 'documents', 'action' => 'upload'), true)) {

            $allowed = array('doc', 'pdf', 'docx');
            if (isset($_FILES['data']['name']['Document']['file']) && $_FILES['data']['error']['Document']['file'] == 0) {

                $extension = pathinfo($_FILES['data']['name']['Document']['file'], PATHINFO_EXTENSION);

                if (!in_array(strtolower($extension), $allowed)) {
                    echo '{"documentstatus" : false , "error": -1}';
                    exit;
                }

                /*
                 * get_temporary_document_name is defined in AppController.php
                 */
                $temporary_document_name = $this->get_temporary_document_name();
                $temporary_document_name = $temporary_document_name . "." . $extension;

                /*
                 * Saving document so that document doesn't gets
                 * lost from the temporary files.
                 */
                if (move_uploaded_file($_FILES['data']['tmp_name']['Document']['file'], Configure::read('upload_location_url') . $temporary_document_name)) {

                    /*
                     * Change this upload from queue instead of uploading here as it takes really long time.
                     * Make this process async.
                     */

                    /*
                     * Upload file to S3 for permanent storage as the current location is temporary only.
                     */
                    $aws_sdk = $this->get_aws_sdk();
//                    $s3_client = $aws_sdk->createS3();
                    $sqs_client = $aws_sdk->createSqs();

                    $uploading_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('upload_queue')));
                    $uploading_queue_localhost_url = $uploading_queue_localhost->get('QueueUrl');

                    $sqs_client->setQueueAttributes(array(
                        'QueueUrl' => $uploading_queue_localhost_url,
                        'Attributes' => array(
                            'VisibilityTimeout' => 30,
                            'ReceiveMessageWaitTimeSeconds' => 5,
                            'DelaySeconds' => 0
                        ),
                    ));

                    $this->add_upload_message_sqs($temporary_document_name, $sqs_client, $uploading_queue_localhost_url);
                    $this->log('going to upload into s3');
                    $this->upload_s3_from_sqs();
                    $this->log('exiting from s3');
                    /*
                     * Add code here for the case when document couldn't be uploaded into S3 but has been successfully uploaded 
                     * in temporary location.
                     * In this case we should log this and should try it in future.
                     */
//                    $upload_result = $s3_client->upload(Configure::read('s3_bucket_name'), $temporary_document_name, fopen(Configure::read('upload_location_url') . $temporary_document_name, 'r'));
                    //echo $file;
                    echo '{"documentstatus" : true '
                    . ',"documentname" : "' . $temporary_document_name . '"'
                    . ',"documentsize":' . $_FILES['data']['size']['Document']['file'] . ''
                    . ',"documentoriginalname":"' . $_FILES['data']['name']['Document']['file'] . '"'
                    . ',"documenttype":"' . $_FILES['data']['type']['Document']['file'] . '"}';
                    exit;
                }
                echo '{"documentstatus" : false , "error":' . $_FILES['data']['error']['Document']['file'] . '}';
            }
            echo '{"documentstatus" : false }';
        }
        echo '{"documentstatus" : false }';
    }

    /*
     * It signs the document.
     */

    public function sign() {

        if ($this->request->is('get')) {
            /*
              Check if the entered url is valid or not
             */
            if (isset($this->params['url']['token']) && isset($this->params['url']['userid']) &&
                    isset($this->params['url']['docuid'])) {

                /*
                 * First checking if the user information has been verified by PAN card etc.
                 * If not we will not let him sign the document.
                 */
                $this->loadModel('Profile');
                $userprofile = $this->Profile->find('first', array('conditions' => array('uid' => $this->params['url']['userid'])));

                if (!isset($userprofile['Profile']['verified']) || $userprofile['Profile']['verified'] != Configure::read('profile_verified')) {
                    $this->Session->setFlash(__('You cant sign document as your profile hasnt been verified.Please visit your profile page to get '
                                    . 'verification information.'), 'flash_error');
                    return $this->redirect(array('controller' => 'users', 'action' => 'index'));
                }
                /*
                  Checking if the current logged in user is the user that was requested to sign the document.
                  Otherwise logout the current user and ask him to login again with the account which was requested to
                  sign
                 */
                if (AuthComponent::user('id')) {
                    if (CakeSession::read("Auth.User.id") === $this->params['url']['userid']) {
                        $token = $this->params['url']['token'];
                        $userid = $this->params['url']['userid'];
                        $docuid = $this->params['url']['docuid'];
                    } else {
                        $this->Auth->logout();
                        $this->Session->setFlash(__('Please login with your account to sign the document.'), 'flash_warning');
                        return $this->redirect(array('controller' => 'users', 'action' => 'index'));
                    }
                }
            } else {
                throw new NotFoundException(__('Invalid URL'));
            }

            /*
              If URL is valid check whether presented variables in URL are present in databse or not
             */
            $this->loadModel('Col');
            $parameters = array(
                'conditions' => array(
                    'uid' => $userid,
                    'token' => $token,
                    'did' => $docuid
                )
            );
            $coldata = $this->Col->find('first', $parameters);

            /*
             * Add code here is the user is unauthorised signatory of the comopany
             */
            if ($coldata) {

                /*
                 * If user has already signed the document but the photo that he supplied hasnt been verified first.
                 */
                if ($coldata['Col']['photoverification'] === Configure::read('photo_unverified')) {
                    $this->Session->setFlash(__('You have already applied for signing this document.PLease let it verify first.
                        After verification you can resign the document.'), 'flash_error');
                    return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
                }

                /*
                 * If user id signing on behalf of company
                 */
                if (isset($coldata['Col']['cid'])) {
                    /*
                     * Checking if user is authorized to sign or not.
                     */
                    $this->loadModel('Compmember');
                    $authorized_check = $this->Compmember->find('count', array('conditions' => array('cid' => $coldata['Col']['cid'], 'uid' => CakeSession::read('Auth.User.id'), 'status' => Configure::read('auth_sign'))));
                    $this->loadModel('Company');
                    $company_info = $this->Company->find('first', array('conditions' => array('id' => $coldata['Col']['cid'])));
                    $this->set('company_info', $company_info);
                    if ($authorized_check === 0) {
                        $this->set('unauthorized', true);
                        $this->render();
                    } else {
                        $this->set('document', $this->Document->findById($docuid));
                        $this->render();
                    }
                }
                /*
                  If all the variables are correct show the document to the user and render the view
                 */
                $this->set('document', $this->Document->findById($docuid));
                $this->render();
                /*
                  Add code here to change the token so that the URL is no longer valid
                 */
            } else {
                $this->Session->setFlash(__('Invalid Request.Please ask the document owner to resend you the signing email
                                     '), 'flash_error');
                return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
            }
        }

        if ($this->request->is('post')) {
            /*
              Storing these values in variables as data would be unset so that these fields dont get saved while
              updating the col and document tables
             */
            $this->request->onlyAllow('ajax');
            $status = $this->request->data['status'];
            $docuid = $this->request->data['docuid'];
            $image_base64 = $this->request->data['image'];

            /*
             * Checking if image that we have captured is not empty.
             */
            if ($image_base64 != '') {

                list($type, $data) = explode(';', $image_base64);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                $imgname = $this->get_temporary_document_name();
                $complete_path = Configure::read('image_upload_location') . $imgname . ".png";
                $file_created = fopen($complete_path, 'w');
                file_put_contents($complete_path, $data);

                /*
                 * Add code here to asynchronusly upload this image into s3 bucket.
                 */
                $uid = $this->request->data['userid'];
                $this->loadModel('Col');
                $parameters = array(
                    'conditions' => array(
                        'uid' => $this->request->data['userid'],
                        'did' => $this->request->data['docuid']
                    ),
                    'fields' => array('id', 'status')
                );
                $coldata = $this->Col->find('first', $parameters);

                /*
                  Set the col id so that it can be updated
                 */
                $this->Col->id = $coldata['Col']['id'];
                $this->Col->set('status', $status);
                /*
                 * Set verification status as unverified and save the image.
                 */
                $this->Col->set('photoverification', Configure::read('photo_unverified'));
                $this->Col->set('photocaptured', $imgname . ".png");
                /*
                  Unset these variables so that they dont get saved.
                 */
                unset($this->request->data['userid']);
                unset($this->request->data['docuid']);
                $this->log('here 1');
                if ($this->Col->save($this->request->data)) {

                    /*
                     * Add code here to send the emails to the support staff to verify this photo.
                     */
                    $aws_sdk = $this->get_aws_sdk();
                    $sqs_client = $aws_sdk->createSqs();

                    $email_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('email_queue')));
                    $email_queue_localhost_url = $email_queue_localhost->get('QueueUrl');

                    $email_to_be_sent = array();
                    $email_to_be_sent['link'] = Router::url(array('controller' => 'documents', 'action' => 'photoverification', '?' => ["did" => $docuid, "uid" => $uid]), true);
                    $email_to_be_sent['title'] = 'Photo Approval';
                    $email_to_be_sent['subject'] = 'Approval of Photo captured';
                    $email_to_be_sent['content'] = 'Please click below link to verify the photo captured.';
                    $email_to_be_sent['button_text'] = 'Verify Photo';

                    $this->loadModel('User');
                    $support_staff = $this->User->find('all', array('conditions' => array('type' => Configure::read('support'))));
                    $this->log('here 2');
                    foreach ($support_staff as $support) {
                        $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $support);
                    }

                    $this->Session->setFlash(__('Your photoscan has been sent for verification.If it gets verified your signature
                                     will get accepted.'), 'flash_success');
                    echo '{"success":true}';    
                } else {
                    /*
                     * Case when data can not be saved.
                     */
                    echo '{"error":1}';
                }
            } else {
                /*
                 * Case when image is not provided.
                 */
                echo '{"error":2}';
            }
        }
    }

    /*
     * This function shows the document , download and trail options and
     * if user is owner of document than edit and delete options also.
     */

    public function show($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid Document'));
        }
        $document = $this->Document->findById($id);

        if (!$document) {
            throw new NotFoundException(__('Invalid Document'));
        }
        $this->set('docudata', $document);
    }

    /*
     * It shows the document trail which contains the history of activities on document.
     * Cureently we need to implement status of name change as well as signatory change.
     * If user changes his sign later on we also need to show that in trail.
     */

    public function trail($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid Document'));
        }
        $document = $this->Document->findById($id);

        if (!$document) {
            throw new NotFoundException(__('Invalid Document'));
        }

        $this->loadModel('User');
        $this->set('ownerdata', $this->User->findById($document['Document']['ownerid']));
        $this->set('docudata', $document);
        $this->loadModel('Col');
        
        /*
         * Add code here to check that status is not doc_owner here so that document owner does not gets listed as 
         * invited signatory in  list of signatories provided.
         */
        $params = array(
            'conditions' => array('did' => $id),
            'order' => array('modified' => -1)
        );
        $cols = $this->Col->find('all', $params);

        if ($cols) {
            $userids = array();
            foreach ($cols as $col):
                array_push($userids, array('_id' => new MongoId($col['Col']['uid'])));
            endforeach;

            $this->loadModel('User');
            $cols_user_data = $this->User->find('all', array('conditions' => array('$or' => $userids)));
            $this->set('cols_user_data', $cols_user_data);

            $id_corresponding_to_name = array();
            foreach ($cols as $col):
                foreach ($cols_user_data as $col_user_data):
                    if ($col_user_data['User']['id'] === $col['Col']['uid']) {
                        $id_corresponding_to_name[$col['Col']['uid']] = $col_user_data['User']['name'];
                    }
                endforeach;
            endforeach;

            $this->set('id_corresponding_to_name', $id_corresponding_to_name);
            $this->set('col_data', $cols);
        }
    }

    /*
     * It deletes the document.
     * If document owner sends the delete request through ajax post than it responds
     * by a JSON object haivng status of deletion.
     */

    /*
     * Add code here to also delete the document from S3 bucket.
     */

    public function delete() {
        $this->request->onlyAllow('ajax');
        if ($this->request->is('post')) {

            $id = $this->request->data['docuid'];

            if (!$id) {
                throw new NotFoundException(__('Invalid Document'));
            }

            $document = $this->Document->findById($id);

            if (!$document) {
                throw new NotFoundException(__('Invalid Document'));
            }

            /*
             * Checking whehter the request generator is owner of the document or not.
             */
            if ($document['Document']['ownerid'] === CakeSession::read('Auth.User.id')) {
                $this->loadModel('Col');

                /*
                 * This object will contain the reuslt of deleting the document
                 */
                $status_object = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);

                /*
                 * First delete from the collabarators table so that even if query fails in between
                 * we would be having original document saved.
                 */
                if ($this->Col->deleteAll(array('did' => $id), false)) {
                    if ($this->Document->delete($id)) {
                        $status_object->status = true;
                        $this->Session->setFlash(__('Document deleted successfully.'), 'flash_success');
                    } else {
                        $status_object->status = false;
                        $this->Session->setFlash(__('Document could not be deleted.Please try again later'), 'flash_error');
                    }
                } else {
                    $this->Session->setFlash(__('Document could not be deleted.Please try again later'), 'flash_error');
                    $status_object->status = false;
                }
            } else {
                $this->Session->setFlash(__('You are not authorised for this request.'), 'flash_error');
                $status_object->status = false;
            }
            $this->set(compact('status_object'));
            $this->set('_serialize', 'status_object');
        }
    }

    /*
     * Shows the edit page to the document owner.
     */

    public function edit($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid Document'));
        }
        $document = $this->Document->findById($id);

        if (!$document) {
            throw new NotFoundException(__('Invalid Document'));
        }

        /*
         * Checking if request sender is document owner or not.
         */
        if ($document['Document']['ownerid'] === CakeSession::read('Auth.User.id')) {
            $this->loadModel('Col');
            $cols = $this->Col->find('all', array('conditions' => array('did' => $document['Document']['id'])));

            if ($cols) {
                $userids = array();
                foreach ($cols as $col):
                    array_push($userids, array('_id' => new MongoId($col['Col']['uid'])));
                endforeach;

                $this->loadModel('User');
                $cols = $this->User->find('all', array('conditions' => array('$or' => $userids), 'fields' => array('name', 'username')));

                $this->Set('cols', $cols);
            }

            $this->set('docudata', $document);
        }
    }

    /*
     * Used for changing document name and signatories.
     * AJAX request from edit page will be sent here.
     */

    public function change_document() {
        $this->request->onlyAllow('ajax');
        $status_object = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);
        /*
         * This flag would be used to check whether we need to change the status of document
         * after addign new new collabarotrs or not.
         */
        $flag_for_changing_document_status = 0;

        if ($this->request->is('post')) {
            $aws_sdk = $this->get_aws_sdk();
            $sqs_client = $aws_sdk->createSqs();

            $email_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('email_queue')));
            $email_queue_localhost_url = $email_queue_localhost->get('QueueUrl');
            $owner_data = $this->User->find('first', array('conditions' => array('id' => CakeSession::read('Auth.User.id'))));
            if (isset($this->request->data['newname'])) {
                /*
                 * Making the default status of name change to be false so that if there is some problem in
                 * completing the transaction completely status would be false
                 */
                $status_object->name = false;
                $newname = $this->request->data['newname'];
            }
            $emails = json_decode($this->request->data['emails']);
            $docuid = $this->request->data['docuid'];

            /*
             * Changing the name of document if its changed
             */
            if (isset($newname)) {
                $this->Document->id = $docuid;
                $this->Document->set('name', $newname);
                $this->Document->save();
                $status_object->name = true;
            }

            $this->loadModel('Col');
            /*
             * Getting all collabarators data
             */
            $cols = $this->Col->find('all', array('conditions' => array('did' => $docuid)));

            /*
             * If collaborators of the document exists
             */
            if ($cols) {
                /*
                 * userids will have ids of all the collabarators
                 */
                $userids = array();
                foreach ($cols as $col):
                    array_push($userids, array('_id' => new MongoId($col['Col']['uid'])));
                endforeach;

                /*
                 * Pushing emails of all the collabarators in colemails
                 */
                $this->loadModel('User');
                $cols = $this->User->find('all', array('conditions' => array('$or' => $userids), 'fields' => array('username')));
                $colemails = [];
                foreach ($cols as $col):
                    array_push($colemails, $col['User']['username']);
                endforeach;
            }

            /*
             * Checking if there is any difference between previous collaborators data and new collaborators data
             * sent by user.
             */
            if (isset($emails) && count($emails) > 0) {
                $temp_array = $emails;
            } else {
                $temp_array = $colemails;
            }

            if (isset($colemails)) {
                array_diff($temp_array, $colemails);
            } else {
                array_diff($temp_array, $emails);
            }
            if ($temp_array) {
                /*
                 * Default status of cols change
                 */
                $status_object->cols = false;
                /*
                 * To edit signatories we will first compare colemails with emails to find common emails and remove
                 * them from both the arrays.
                 * These emails would already be present in cols table so no need to edit them.
                 * Now colemails would be having emails of collabarators which are to be removed from the cols table i.e
                 * user has removed these collabarators.
                 * emails will be having emails of the new signatories i.e new signatories added by the user.
                 *
                 * In the end if any new signatories are added or some existing ones are removed we will change the status
                 * of the original document to pending and will again send the signing email to all the
                 * collabarators.
                 */

                /*
                 * Checking if all the emails are valid i.e. user is not tampering with data otherwise keep
                 * user in risky category because the user is trying to hack.
                 */
                foreach ($emails as $email):
                    if (!Validation::email($email)) {
                        $status_object->cols = false;
                    }
                endforeach;

                foreach ($emails as $email):
                    if (isset($colemails) && in_array($email, $colemails)) {
                        /*
                          Removing common emails from both the arrays
                         */
                        $ind_in_emails = array_search($email, $emails);
                        $ind_in_colemails = array_search($email, $colemails);
                        array_splice($emails, $ind_in_emails, 1);

                        array_splice($colemails, $ind_in_colemails, 1);
                    } else {
                        $flag_for_changing_document_status = 1;
                        /*
                          Adding uncommon emails into the cols table
                         */
                        $this->Col->create();
                        $this->Col->set('did', $docuid);
                        $user_with_this_email = $this->User->find('first', array('conditions' => array('username' => $email)));
                        $this->Col->set('uid', $user_with_this_email['User']['id']);
                        $token = $this->generate_token($email, $user_with_this_email['User']['name']);
                        $this->Col->set('token', $token);
                        $this->Col->set('status', "0");
                        $this->Col->save();

                        /*
                          Add code here for sending signing link to the new collaborators
                         */
                        $document_signing_link = Router::url(array('controller' => 'documents',
                                    'action' => 'sign',
                                    "?" => [
                                        "userid" => $user_with_this_email['User']['id']
                                        , "token" => $token
                                        , "docuid" => $docuid])
                                        , true);
                        $email_to_be_sent = array();
                        $email_to_be_sent['link'] = $document_signing_link;
                        $email_to_be_sent['title'] = 'Document SIgning Request';
                        $email_to_be_sent['subject'] = 'Document Signing Request';
                        $email_to_be_sent['content'] = 'You have been requested by ' . $owner_data['User']['name'] . ' to sign a document on our website.Please '
                                . 'click below button to view your document signing requests.';
                        $email_to_be_sent['button_text'] = 'View Document Signing Request';
                        $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $user_with_this_email);
//                        $this->sendemail('sign_document_request', 'notification_email_layout', $user_with_this_email, $document_signing_link, 'Document Signing Request');
                    }
                endforeach;

                /*
                  If some emails remain the colemails array
                 */
                if (isset($colemails) && count($colemails) > 0) {
                    $flag_for_changing_document_status = 1;
                    /*
                      Delete the collaborators having emails remaining in colemails.
                     */
                    $cols_to_be_removed_ids = array();
                    $cols_to_be_removed_emails = array();

                    foreach ($colemails as $colemail):
                        array_push($cols_to_be_removed_emails, array('username' => $colemail));
                    endforeach;

                    $params = array(
                        'conditions' => array('$or' => $cols_to_be_removed_emails),
                        'fields' => array('id')
                    );

                    $user_ids = $this->User->find('all', $params);

                    foreach ($user_ids as $user_id):
                        array_push($cols_to_be_removed_ids, array('uid' => $user_id['User']['id']));
                    endforeach;

                    $id_in_cols_table = $this->Col->find('all', array('conditions' => array('$or' => $cols_to_be_removed_ids, 'did' => $docuid)));

                    foreach ($id_in_cols_table as $id):
                        $this->Col->delete($id['Col']['id']);
                    endforeach;
                }
                if ($flag_for_changing_document_status === 1) {
                    /*
                     * Change status of document to pending as some new collabarators have been
                     * added or some had been removed.
                     */
                    $this->Document->id = $docuid;
                    $this->Document->set('status', "0");
                    $this->Document->save();
                }
                $status_object->cols = true;
            }
            $this->send_email_from_sqs();
            $status_object->status = true;
            $this->Session->setFlash(__('Data saved successfully.'), 'flash_success');
        } else {
            $this->Session->setFlash(__('Wrong request.'), 'flash_error');
            $status_object->status = false;
        }

        $this->set(compact('status_object'));
        $this->set('_serialize', 'status_object');
    }

    /*
     * To preview the document.
     * Only available to the document owner and its collabarators.
     */

    public function preview() {

        $this->layout = false;
        /*
         * This flag variable is needed for the case when document is shown while uploading
         * As at that time document doesnt gets saved into the database so we need to
         * skip checking for that.
         */
        $nochecking_flag = 0;

        if (isset($this->params['url']['name']) && isset($this->params['url']['type'])) {
            $docname = $this->params['url']['name'];
            $extension = $this->params['url']['type'];
            /*
             * status would be set to temp if document is needed to be viewed while uploading.
             *
             * One bug is there that when document doesnt gets saved into database but remains saved
             * in our location anyone can view that document by setting status variable to temp.
             */
            if (isset($this->params['url']['status']) && $this->params['url']['status'] === 'temp') {
                /*
                 * Set the nochecking flag so that we skip checking if user is allowed to view document
                 * or not.
                 */
                $nochecking_flag = 1;
            }
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }

        /*
         * Change this url according to the location where document is getting saved.
         */
        $docurl = Configure::read('upload_location_url') . $docname . '.' . $extension;
        $file_check = file_exists($docurl);

        /*
         * If file isnt in temporary location check file existence in S3
         */
        if (!$file_check) {
            $this->log('here 1');
            $aws_sdk = $this->get_aws_sdk();
            $s3_client = $aws_sdk->createS3();

            $file_in_s3 = $s3_client->doesObjectExist(Configure::read('s3_bucket_name'), $docname . '.' . $extension);

            if ($file_in_s3) {
                $this->log('here 2');
                /*
                 * If file is present in S3 download it in temporary location.
                 */
                $result = $s3_client->getObject(array(
                    'Bucket' => Configure::read('s3_bucket_name'),
                    'Key' => $docname . '.' . $extension,
                    'SaveAs' => $docurl
                ));
//                $file_key = $docname . '.' . $extension;
//                $furl = "{Configure::read('s3_bucket_name')}/{$file_key}";
//                $request = $s3_client->get($furl);
//                $docurl = $s3_client->createPresignedUrl($request,'+10 minutes');
////                $docurl = $s3_client->getObjectUrl(Configure::read('s3_bucket_name'), $docname . '.' . $extension,'+10 minutes');
//                echo $docurl;
//                $this->log($docurl);
                /*
                 * When we cant download the document from S3.
                 */
                if (!$result) {
                    throw new NotFoundException(__('Cant download document.Please try again later.'));
                }
            } else {
                /*
                 * When doc is not present even in S3.
                 */
                throw new NotFoundException(__('Invalid URL'));
            }
        }

        $colids = [];

        $this->loadModel('Col');
        /*
         * Finding info about the document
         */
        $docinfo = $this->Document->find('first', array('conditions' => array('originalname' => $docname . '.' . $extension)));
        /*
         * If any info is found out related to the document.
         */
        if (isset($docinfo) && sizeof($docinfo) > 0) {
            /*
             * If any info related to the doc is find out unset the nochecking flag even if the
             * status is set temp.
             * Temp status would work only when document doesnt gets saved in database.
             */
            $nochecking_flag = 0;
            $cols = $this->Col->find('all', array('conditions' => array('did' => $docinfo['Document']['id'])));
//            debug($cols);
            /*
             * colids would contain userids of all the collabarators.
             */
            $colids = [];
            array_push($colids,$docinfo['Document']['ownerid']);
            foreach ($cols as $col):
                if (isset($col['Col']['cid'])) {
                    /*
                     * Checking if user is authorized to sign or not on behalf of company.
                     */
                    $this->loadModel('Compmember');
                    $status = array();
                    array_push($status, array('status' => Configure::read('legal_head')));
                    array_push($status, array('status' => Configure::read('auth_sign')));
                    $authorized_check = $this->Compmember->find('count', array('conditions' => array('cid' => $col['Col']['cid'], 'uid' => $col['Col']['uid'], '$or' => $status)));
                    if ($authorized_check != 0) {
                        array_push($colids, $col['Col']['uid']);
                    }
                } else {
                    array_push($colids, $col['Col']['uid']);
                }
            endforeach;
        }
        /*
         * If userid of current logged in user falls in colids array or the request is having temp
         * status than show the doc to user else show 404 error.
         */
        if (in_array(CakeSession::read('Auth.User.id'), $colids) || $nochecking_flag === 1) {

            if (strtolower($extension) === 'pdf') {
                header('Content-Type: application/pdf');
            } elseif (strtolower($extension) === 'doc' || strtolower($extension) === 'docx') {
                header('Content-Type: application/doc');
            }

            header("Content-Length: " . filesize($docurl));
            readfile($docurl);
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }
    }

    /*
     * To download the document .
     * Only available to document owners and collabarators .
     * No temp status available.
     */

    public function download() {
        /*
         * Same code as preview .
         * Only change is that no temp request is available.
         * Also one extra header is addded to download the document directly.
         */
        $this->layout = false;

        if (isset($this->params['url']['name']) && isset($this->params['url']['type'])) {
            $docname = $this->params['url']['name'];
            $extension = $this->params['url']['type'];
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }

        $docurl = Configure::read('upload_location_url') . $docname . '.' . $extension;
        $file_check = file_exists($docurl);

        /*
         * If file isnt in temporary location check file existence in S3
         */
        if (!$file_check) {
            $aws_sdk = $this->get_aws_sdk();
            $s3_client = $aws_sdk->createS3();

            $file_in_s3 = $s3_client->doesObjectExist(Configure::read('s3_bucket_name'), $docname . '.' . $extension);

            if ($file_in_s3) {
                /*
                 * If file is present in S3 download it in temporary location.
                 */
                $result = $s3_client->getObject(array(
                    'Bucket' => Configure::read('s3_bucket_name'),
                    'Key' => $docname . '.' . $extension,
                    'SaveAs' => $docurl
                ));

                /*
                 * When we cant download the document from S3.
                 */
                if (!$result) {
                    throw new NotFoundException(__('Cant download document.Please try again later.'));
                }
            } else {
                /*
                 * When doc is not present even in S3.
                 */
                throw new NotFoundException(__('Invalid URL'));
            }
        }

        $this->loadModel('Col');
        /*
         * Finding info about the document
         */
        $docinfo = $this->Document->find('first', array('conditions' => array('originalname' => $docname . '.' . $extension)));

        if (isset($docinfo) && sizeof($docinfo) > 0) {

            $cols = $this->Col->find('all', array('conditions' => array('did' => $docinfo['Document']['id'])));

            $colids = [];
            array_push($colids,$docinfo['Document']['ownerid']);
            foreach ($cols as $col):
                if (isset($col['Col']['cid'])) {
                    /*
                     * Checking if user is authorized to sign or not on behalf of company.
                     */
                    $this->loadModel('Compmember');
                    $status = array();
                    array_push($status, array('status' => Configure::read('legal_head')));
                    array_push($status, array('status' => Configure::read('auth_sign')));
                    $authorized_check = $this->Compmember->find('count', array('conditions' => array('cid' => $col['Col']['cid'], 'uid' => $col['Col']['uid'], '$or' => $status)));
                    if ($authorized_check != 0) {
                        array_push($colids, $col['Col']['uid']);
                    }
                } else {
                    array_push($colids, $col['Col']['uid']);
                }
            endforeach;
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }

        $docdata = $this->Document->find('first', array('conditions' => array('originalname' => $docname . "." . $extension)));

        if ($docdata['Document']['status'] === Configure::read('doc_completed')) {
            /*
             * Including signatories in the document if the document is completed.
             */
            $signedpdf = Configure::read('upload_location_url') . $docname . 'signed.' . $extension;
            $check = file_exists($signedpdf);

            if (!$check) {
                /*
                 * If signed document is not present.
                 */
                $this->loadModel('Profile');

                $pdf = new PDF();
                $pdffile = $docurl;
                $pdf->SetFont('Times', '', 12);
                $pdf->AddPage();
                $pdf->SetAutoPageBreak(TRUE, 10);
                $i = 0;
                foreach ($cols as $col) {
                    if ($i >= 7) {
                        $pdf->AddPage();
                        $i = 0;
                    }
                    $this->loadModel('User');
                    $profiledata = $this->Profile->find('first', array('conditions' => array('uid' => $col['Col']['uid'])));
                    $userdata = $this->User->find('first', array('conditions' => array('id' => $col['Col']['uid'])));
                    $pdf->Image(Configure::read('image_upload_location') . $profiledata['Profile']['verificationimage'], 10, 30 * ($i + 1) + 5 * $i, 30);
                    $pdf->SetXY(80, 30 * ($i + 1) + 5 * $i);
                    $pdf->Cell(50, 10, $userdata['User']['name'], 0, 0, 'C');
                    $pdf->SetXY(80, 30 * ($i + 1) + 20 + 5 * $i);
                    $pdf->Cell(50, 10, 'Face Score', 0, 0, 'C');
                    $i+=1;
                }

                $pdf->Output($signedpdf);
                $PDF = new PDFMerger();
                $PDF->addPDF($pdffile, 'all');
                $PDF->addPDF($signedpdf, 'all');
                $PDF->merge('file', $signedpdf);
                $docurl = $signedpdf;
            }
            else
            {
                /*
                 * If signed document is already present.
                 */
                $docurl = $signedpdf;
            }
        }

        if (in_array(CakeSession::read('Auth.User.id'), $colids)) {

            if (strtolower($extension) === 'pdf') {

                header('Content-Type: application/pdf');
                header("Content-Disposition:attachment;filename='" . $docinfo['Document']['name'] . "_from_signzy." . $extension . "'");
            } elseif (strtolower($extension) === 'doc' || strtolower($extension) === 'docx') {

                header('Content-Type: application/doc');
                header("Content-Disposition:attachment;filename='" . $docinfo['Document']['name'] . "_from_signzy." . $extension . "'");
            }

            header("Content-Length: " . filesize($docurl));
            readfile($docurl);
        } else {
            throw new NotFoundException(__('Invalid URL'));
        }
    }

    public function photoverification() {
        $this->loadModel('User');
        $userid = CakeSession::read('Auth.User.id');
        $userdata = $this->User->find('first', array('conditions' => array('id' => $userid)));

        /*
         * Checking if the user is not customer
         */
        if (isset($userdata['User']['type']) && $userdata['User']['type'] != Configure::read('customer')) {
            if ($this->request->is('get')) {
                if (isset($this->params['url']['uid']) && isset($this->params['url']['did'])) {
                    $uid = $this->params['url']['uid'];
                    $did = $this->params['url']['did'];
                } else {
                    throw new NotFoundException(__('Invalid URL'));
                }

                $this->loadModel('Profile');
                $signeeprofile = $this->Profile->find('first', array('conditions' => array('uid' => $uid)));

                $this->loadModel('Col');
                $this->loadModel('Document');
                $signeecol = $this->Col->find('first', array('conditions' => array('uid' => $uid, 'did' => $did)));

                $this->log($signeeprofile);
                $this->log($signeecol);
                $this->set('profiledata', $signeeprofile);
                $this->set('coldata', $signeecol);
            }
            if ($this->request->is('post')) {
                $this->request->allowMethod('ajax');
                $this->autorender = false;
                $this->layout = false;

                $option = $this->request->data['option'];
                $uid = $this->request->data['uid'];
                $did = $this->request->data['did'];

                $this->loadModel('Col');
                $coldata = $this->Col->find('first', array('conditions' => array('uid' => $uid, 'did' => $did)));

                $aws_sdk = $this->get_aws_sdk();
                $sqs_client = $aws_sdk->createSqs();

                $email_queue_localhost = $sqs_client->createQueue(array('QueueName' => Configure::read('email_queue')));
                $email_queue_localhost_url = $email_queue_localhost->get('QueueUrl');

                $this->loadModel('User');
                $this->loadModel('Col');
                $userdata = $this->User->find('first', array('conditions' => array('id' => $uid)));
                if ($option === 'TRUE') {
                    $this->Col->id = $coldata['Col']['id'];
                    $this->Col->set('photoverification', Configure::read('photo_verified'));
                    if ($this->Col->save()) {
                        /*
                         * Send email to the user that he has been verified.
                         */
                        $email_to_be_sent = array();
                        $email_to_be_sent['link'] = Router::url(array('controller' => 'documents', 'action' => 'index'), true);
                        $email_to_be_sent['title'] = 'Photo verified';
                        $email_to_be_sent['subject'] = 'Photo verified';
                        $email_to_be_sent['content'] = 'Your photo captured has been verified which we captured during signing '
                                . 'of a doc.Please click below button to see your documents.';
                        $email_to_be_sent['button_text'] = 'My Documents';

                        $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $userdata);
                        $this->update_document_status($did, $coldata['Col']['status']);

                        echo '{"success":1}';
                    } else {
                        echo '{"error":1}';
                    }
                } else {
                    $this->Col->id = $coldata['Col']['id'];
                    $this->Col->set('photoverification', Configure::read('photo_rejected'));

                    if ($this->Col->save()) {
                        /*
                         * Send email to the user and document owner that user signatory has been rejected.
                         */
                        $email_to_be_sent = array();
                        $email_to_be_sent['link'] = Router::url(array('controller' => 'documents', 'action' => 'index'), true);
                        $email_to_be_sent['title'] = 'Photo rejected';
                        $email_to_be_sent['subject'] = 'Photo rejected';
                        $email_to_be_sent['content'] = 'Your photo captured has been rejected which we captured during signing '
                                . 'of a doc.Please visit previous emails to reapply.';
                        $email_to_be_sent['button_text'] = 'My Documents';
                        $this->add_email_message_sqs($email_to_be_sent, $sqs_client, $email_queue_localhost_url, $userdata);

                        echo '{"success":2}';
                    } else {
                        echo '{"error":1}';
                    }
                }
            }
        }
        else{
            throw new NotFoundException('Invalid URL');
        }
    }

}
