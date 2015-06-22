<?php

App::uses('CakeEmail', 'Network/Email');

class Document extends AppModel {

    var $actsAs = array(
        'UploadPack.Upload' => array(
            'avatar' => array(
            )
        )
    );
    var $validate = array(
        'avatar' => array(
            'maxSize' => array(
                'rule' => array('attachmentMaxSize', 104857600),
                'message' => 'Contract can\'t be larger than 100MB'
            ),
            'minSize' => array(
                'rule' => array('attachmentMinSize', 1024),
                'message' => 'Contract can\'t be smaller than 1KB'
            ),
            'image3' => array(
                'rule' => array('attachmentContentType', array('application/pdf', 'application/msword')),
                'message' => 'Only pdfs or doc please'
            ),
            'checkIniSizeError' => array(
                'rule' => array('phpUploadError', UPLOAD_ERR_INI_SIZE),
                'message' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini'
            ),
            'checkSizesError' => array(
                'rule' => array('phpUploadError', array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE)),
                'message' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini or MAX_FILE_SIZE directive that was specified in the HTML form'
            ),
            'checkAllError' => array(
                'rule' => array('phpUploadError'),
                'message' => 'Can\'t upload'
            )
        )
    );

    public function send_signing_email($userdata, $token, $docid) {
        $sign_document_email = new CakeEmail('mandrill_signup');
        $sign_document_email->to($userdata['User']['username']);
        $sign_document_email->subject('Document Signing Request');
        $sign_document_email->template('sign_document_request', 'notification_email_layout')
                ->viewVars(array('document_signing_link' => Router::url(array('controller' => 'documents',
                        'action' => 'sign',
                        '?' => [
                            'userid' => $userdata['User']['id'], 'token' => $token, 'docuid' => $docid,],), true),
                    'name_of_user' => $userdata['User']['name'],));
        if ($sign_document_email->send()) {
            return true;
        }
        return false;
    }

}

?>
