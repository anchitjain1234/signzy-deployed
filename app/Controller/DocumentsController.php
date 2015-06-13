<?php
App::uses('CakeEmail', 'Network/Email');

class DocumentsController extends AppController
{

  public function beforeFilter()
	{
		/*
		To give the name ofthe user to the layout befire the view is loaded.
		*/
    $this->loadModel('User');

    $uid = CakeSession::read("Auth.User.id");
    $params = array(
      'fields' => array('name', 'verified'),
      'conditions' => array('id' => $uid),
    );
    $userdata=$this->User->find('first',$params);
    $this->set('name',$userdata['User']['name']);
	}

  public $uses = array('Document');
  var $helpers = array('Form', 'UploadPack.Upload');
  public function index()
  {
    $this->layout = 'insidelayout';
    $uid = CakeSession::read("Auth.User.id");
    $parameters = array(
      'fields' => array('id','avatar_file_name','name'),
      'conditions' => array('ownerid'=> $uid)
    );
    $this->set('user_documents_data',$this->Document->find('all',$parameters));

  }

  public function display() {
  /* form submitted? */
    if ($this->request->is('post')) {
         $filename = "/var/www/html/cakephp/app/webroot/files/".$this->data['Documents']['file']['name'];
         /* copy uploaded file */
        if (move_uploaded_file($this->data['Documents']['file']['tmp_name'],$filename)) {
          /* save message to session */
          print_r($_FILES);
          $im = new imagick('file.pdf[0]');
          $im->setImageFormat('jpg');
          header('Content-Type: image/jpeg');
          echo $im;
          $this->Session->setFlash('File uploaded successfuly. You can view it <a href="http://localhost/cakephp/files/'.$this->data['Documents']['file']['name'].'">here</a>.');
          /* redirect */
          //$this->redirect(array('action' => 'index'));
        } else {
          /* save message to session */
          $this->Session->setFlash('There was a problem uploading file. Please try again.');
        }
   }
  $this->render('home');
}

public function example1()
{
  if($this->request->is('post'))
  {
    print_r($this->Document);
    $this->Document->save();
  }

}

function create() {
		if (!empty($this->data)) {
			$this->Document->create($this->data);
			if ($this->Document->save()) {
				$this->redirect('/documents/show/'.$this->Document->id);
			}
      else
      {
        echo "error";
      }
		}
	}

	function show($id) {
		$this->set('document', $this->Document->findById($id));
	}

  public function upload()
  {
    $this->layout = 'insidelayout';
  }

  public function upload2()
  {
    $this->layout = 'insidelayout';
    if (!empty($this->data))
    {
			$this->Document->create($this->data);

      $this->request->data['Document']['ownerid']= CakeSession::read("Auth.User.id");
			if ($this->Document->save($this->request->data))
      {
        $this->set('document', $this->Document->findById($this->Document->id));
			}
      else
      {
        echo "error";
      }
		}
  }

}

?>
