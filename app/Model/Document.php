<?php
App::uses('CakeEmail', 'Network/Email');

class Document extends AppModel
{
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
			'rule' => array('attachmentContentType', array('application/pdf','application/msword')),
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

}

?>
