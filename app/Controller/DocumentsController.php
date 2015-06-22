<?php

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

    public function index() {
        /*
          To display all the previous uploaded documents of the user as well as the documents in which the
          user id requested to sign it.
         */

        $uid = CakeSession::read('Auth.User.id');
        $parameters = array(
            'fields' => array('id', 'avatar_file_name', 'name', 'created', 'status'),
            'conditions' => array('ownerid' => $uid),
        );
        $user_documents_data = $this->Document->find('all', $parameters);

        /*
          Now finding documents in which user is a collabarator
         */
        $this->loadModel('Col');
        $parameters = array(
            'fields' => array('did'),
            'conditions' => array('uid' => $uid),
        );
        $coldata = $this->Col->find('all', $parameters);
        if ($coldata) {
            foreach ($coldata as $col):
                $parameters = array(
                    'fields' => array('id', 'avatar_file_name', 'name', 'created', 'status'),
                    'conditions' => array('id' => $col['Col']['did']),
                );
                array_push($user_documents_data, $this->Document->find('first', $parameters));
            endforeach;
        }
        $this->set('user_documents_data', $user_documents_data);
    }

    /*
      This function is upload2 because it is using native php uploading not the jquery uload which would be
      used in prodcution version.
      This would be most likely to be used in the outdated browsers which do not support jquery or has
      less support for it.
     */

    public function upload2() {
        if ($this->request->is('post')) {
            //$data = $this->request->input('json_decode',true);
            //$emails  = json_decode($this->request->data['emails_hidden']);
            //$this->log($emails);
            //print_r($emails);
            //print_r($this->request->data);

            if (!empty($this->request->data)) {
                $this->Document->create($this->data);
                $this->request->data['Document']['ownerid'] = CakeSession::read('Auth.User.id');

                /*
                  If Document is valid i.e. its pdf or doc and has size<100 MB
                 */
                if ($this->Document->validates()) {
                    $this->request->data['Document']['status'] = '0';
                    if ($this->Document->save($this->request->data)) {
                        /*
                          If Document is saved successfully than we can save each collabarator in col model
                         */
                        $this->loadModel('Col');
                        $emails = json_decode($this->request->data['emails_hidden']);

                        /*
                          If any signatory has been added or not
                         */
                        if (count($emails) > 0) {
                            $document_name = $this->request->data['Document']['name'];

                            /*
                              Issue here we should make document name unique for each user
                              Get document id where ownerid = current user id and name = current uploaded document name
                             */
                            $params = array(
                                'fields' => array('id'),
                                'conditions' => array('name' => $document_name, 'ownerid' => CakeSession::read('Auth.User.id')),
                            );
                            $docudata = $this->Document->find('first', $params);

                            /*
                              For each entered email address we will first check whether its a valid email address or not.
                              If we find even if one email is invalid it means that user is manipulating our javascript so
                              we will not save the collabarators data for this request.
                             */
                            foreach ($emails as $email):
                                if (!Validation::email($email)) {
                                    throw new NotFoundException(__('Error while saving data.'));
                                }
                            endforeach;

                            foreach ($emails as $email):
                                $this->Col->create();
                                $this->Col->set('did', $docudata['Document']['id']);

                                /*
                                  Change status here to default status
                                 */
                                $this->Col->set('status', '0');
                                $params = array(
                                    'fields' => array('id', 'name'),
                                    'conditions' => array('username' => $email),
                                );
                                $userdata = $this->User->find('first', $params);
                                $token = str_shuffle(hash('sha512', (hash('sha256', $email
                                                        . $userdata['User']['name'])) . strval(time()) . md5(rand())));
                                $this->Col->set('token', $token);
                                $this->Col->set('uid', $userdata['User']['id']);
                                $this->Col->save();

                                /*
                                  Send email to the saved collabarator
                                 */
                                $sign_document_email = new CakeEmail('mandrill_signup');
                                $sign_document_email->to($email);
                                $sign_document_email->subject('Document Signing Request');
                                $sign_document_email->template('sign_document_request', 'notification_email_layout')
                                        ->viewVars(array('document_signing_link' => Router::url(array('controller' => 'documents',
                                                'action' => 'sign',
                                                '?' => [
                                                    'userid' => $userdata['User']['id'], 'token' => $token, 'docuid' => $docudata['Document']['id'],],), true),
                                            'name_of_user' => $userdata['User']['name'],));
                                $sign_document_email->send();

                            endforeach;
                            $this->Session->setFlash(__('Document uploaded and emails sent to all the signatories successfully.'), 'flash_success');
                        } else {
                            $this->Session->setFlash(__('Document uploaded successfully.'), 'flash_success');
                        }

                        $this->set('document', $this->Document->findById($this->Document->id));
                    } else {
                        $this->Session->setFlash(__('Your document couldn\'t be uploaded.'), 'flash_error');
                    }
                } else {
                    $this->Session->setFlash(__('Your document couldn\'t be uploaded.'), 'flash_error');
                }
            }
        }
    }

    public function sign() {
        if ($this->request->is('get')) {
            /*
              Check if the entered url is valid or not
             */
            if (isset($this->params['url']['token']) && isset($this->params['url']['userid']) &&
                    isset($this->params['url']['docuid'])) {
                /*
                  Checking if the current logged in user is the user that was requested to sign the document.
                  Otherwise logout the current user and ask him to login again with the account which was requested to
                  sign
                 */
                if (AuthComponent::user('id')) {
                    if (CakeSession::read('Auth.User.id') === $this->params['url']['userid']) {
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
                    'did' => $docuid,
                ),
                'fields' => array('id', 'status'),
            );
            $coldata = $this->Col->find('first', $parameters);

            if ($coldata) {
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
            $status = $this->request->data['status'];
            $docuid = $this->request->data['docuid'];

            $this->loadModel('Col');
            $parameters = array(
                'conditions' => array(
                    'uid' => $this->request->data['userid'],
                    'did' => $this->request->data['docuid'],
                ),
                'fields' => array('id', 'status'),
            );
            $coldata = $this->Col->find('first', $parameters);

            /*
              Set the col id so that it can be updated
             */
            $this->Col->id = $coldata['Col']['id'];
            $this->Col->set('status', $userdata['User']['id']);

            /*
              Unset these variables so that they dont get saved in the databse.
             */
            unset($this->request->data['userid']);
            unset($this->request->data['docuid']);

            if ($this->Col->save($this->request->data)) {
                $total_collabarators = $this->Col->find('count', array('conditions' => array('did' => $docuid)));
                /*
                  See here if he document has to be rejected even if one user rejects it or not,
                  Checking if current user voided or rejected the document
                 */
                if ($status === '2' || $status === '3') {
                    /*
                      Even if one user voids or rejects the document whole document is voided or rejected
                     */
                    $this->Document->id = $docuid;
                    $this->Document->set('status', $status);
                    $this->Document->save();
                }
                /*
                  Checking if current user signed the document.
                 */ elseif ($status === '1') {
                    /*
                      If all the signatories sign the document than only document will have status complete i.e. 1
                     */
                    $parameters = array(
                        'conditions' => array(
                            'did' => $docuid,
                            'status' => '1',
                        ),
                    );
                    $collabarators_with_completed_status = $this->Col->find('count', $parameters);

                    /*
                      Checking if document signing has been completed or not.
                     */
                    if ($collabarators_with_completed_status === $total_collabarators) {
                        $this->Document->id = $docuid;
                        $this->Document->set('status', '1');
                        $this->Document->save();
                    }
                }

                /*
                  Sending the notification email to the owner that there has been some changes in document.
                  Letting him to know to visit the dashboard
                  Will add the option in future to disable email alert for every status update.
                  Also include here to send the emails to all the other collabarators also to notify them of the change.
                 */
                $parameters = array(
                    'conditions' => array(
                        'id' => $docuid,
                    ),
                    'fields' => array('ownerid'),
                );
                $owner_id = $this->Document->find('first', $parameters);

                $parameters = array(
                    'conditions' => array(
                        'id' => $owner_id['Document']['ownerid'],
                    ),
                    'fields' => array('username', 'name'),
                );

                $owner_data = $this->User->find('first', $parameters);
                $document_change_email = new CakeEmail('mandrill_signup');
                $document_change_email->to($owner_data['User']['username']);
                $document_change_email->subject('Document Status Updated');
                $document_change_email->template('document_updated_request', 'notification_email_layout')
                        ->viewVars(array('dashboard_link' => Router::url(array('controller' => 'dashboard',
                                'action' => 'index',), true),
                            'name_of_user' => $owner_data['User']['name'],));
                $document_change_email->send();

                return $this->Session->setFlash(__('Your status updated successfully.
																		'), 'flash_success');
            } else {
                return $this->Session->setFlash(__('Error while saving your data.Please try again later.
																		'), 'flash_error');
            }
        }
    }

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
            foreach($cols as $col):
                foreach($cols_user_data as $col_user_data):
                    if($col_user_data['User']['id'] === $col['Col']['uid'])
                    {
                        $id_corresponding_to_name[$col['Col']['uid']] = $col_user_data['User']['name'];
                    }
                endforeach;
            endforeach;
            
            $this->set('id_corresponding_to_name',$id_corresponding_to_name);
            $this->set('col_data', $cols);
        }
    }

    public function delete() {
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
              Checking whehter the request generator is owner of the document or not.
             */
            if ($document['Document']['ownerid'] === CakeSession::read('Auth.User.id')) {
                $this->loadModel('Col');

                /*
                  This object will contain the reuslt of deleting the document
                 */
                $status_object = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);

                /*
                  First delete from the collabarators table so that even if query fails in between
                  we would be having original document saved.
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

    public function edit($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid Document'));
        }
        $document = $this->Document->findById($id);

        if (!$document) {
            throw new NotFoundException(__('Invalid Document'));
        }

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

    public function change_document() {
        $status_object = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);
        /*
         * This flag would be used to check whether we need to change the status of document
         * after addign new new collabarotrs or not.
         */
        $flag_for_changing_document_status =0 ;
        
        if ($this->request->is('post')) {
            if (isset($this->request->data['newname'])) {
                /*
                  Making the default status of name change to be false so that if there is some problem in
                  completing the transaction completely status would be false
                 */
                $status_object->name = false;
                $newname = $this->request->data['newname'];
            }
            $emails = json_decode($this->request->data['emails']);
            $docuid = $this->request->data['docuid'];

            /*
              Changing the name of document if its changed
             */
            if (isset($newname)) {
                $this->Document->id = $docuid;
                $this->Document->set('name', $newname);
                $this->Document->save();
                $status_object->name = true;
            }

            $this->loadModel('Col');
            /*
              Getting all collabarators data
             */
            $cols = $this->Col->find('all', array('conditions' => array('did' => $docuid)));

            /*
              If collaborators of the document exists
             */
            if ($cols) {
                /*
                  userids will have ids of all the collabarators
                 */
                $userids = array();
                foreach ($cols as $col):
                    array_push($userids, array('_id' => new MongoId($col['Col']['uid'])));
                endforeach;

                /*
                  Pushing emails of all the collabarators in colemails
                 */
                $this->loadModel('User');
                $cols = $this->User->find('all', array('conditions' => array('$or' => $userids), 'fields' => array('username')));
                $colemails = [];
                foreach ($cols as $col):
                    array_push($colemails, $col['User']['username']);
                endforeach;
            }

            /*
              Checking if there is any difference between previous collaborators data and new collaborators data
              sent by user.
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
                  Default status of cols change
                 */
                $status_object->cols = false;
                /*
                  To edit signatories we will first compare colemails with emails to find common emails and remove
                  them from both the arrays.
                  These emails would already be present in cols table so no need to edit them.
                  Now colemails would be having emails of collabarators which are to be removed from the cols table i.e
                  user has removed these collabarators.
                  emails will be having emails of the new signatories i.e new signatories added by the user.
                 * 
                 * In the end if any new signatories are added or some existing ones are removed we will change the status
                 * of the original document to pending and will again send the signing email to all the 
                 * collabarators.
                 */

                /*
                  Checking if all the emails are valid i.e. user is not tampering with data otherwise keep
                  user in risky category because the user is trying to hack.
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
                        $token = str_shuffle(hash('sha512', (hash('sha256', $email
                                                . $user_with_this_email['User']['name'])) . strval(time()) . md5(rand())));
                        $this->Col->set('token', $token);
                        $this->Col->set('status', "0");
                        $this->Col->save();

                        /*
                          Add code here for sending signing link to the new collaborators
                         */
                        $this->Document->send_signing_email($user_with_this_email, $token, $docuid);
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
                if($flag_for_changing_document_status === 1)
                {
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
            $status_object->status = true;
            $this->Session->setFlash(__('Data saved successfully.'), 'flash_success');
        }
        else {
            $this->Session->setFlash(__('Wrong request.'), 'flash_error');
            $status_object->status = false;
        }

        $this->set(compact('status_object'));
        $this->set('_serialize', 'status_object');
    }

}
