<?php

class Home extends AppModel
{
    public $name = 'Home';                        //Name of the model
    public $primarykey = '_id';


    public $validate = array (
      'username' => array (
        'alphaNumeric' => array(
          'rule' => 'alphaNumeric',
          'required' => true,
          'message' => 'Name can contain letters and numerals only'
          )
        ),
      'email'=>array(
        'isUnique'=>array(
          'rule'=>array('checkemail'),
          'message'=>'Entered email address is already registered.Please enter unique email'
          ),
        'email'=>array(
          'rule'=>'email',
          'message'=>'Please give a valid email address'
          ),
        'required'=>array(
          'rule'=>'notEmpty',
          'message'=>'Please enter your email addreess'
          )
        ),
      'password'=>array(
        'required'=>array(
          'rule'=>'notEmpty',
          'message'=>'Please enter password',

          ),
        'size'=>array(
          'rule' => array('minLength', '8'),
          'message' => 'Password of minimum length of 8 characters is required'
          )
        ),
      'password_repeat'=>array(
        'required'=>array(
          'rule'=>'notEmpty',
          'message'=>'Please enter password',
          ),
        'equal'=>array(
          'rule'=>array('pwdequal'),
          'message'=>"Passwords don't match"
          )
        )
      );

public function beforeSave($options = array()) 
{
  if (isset($this->data[$this->alias]['password']) && isset($this->data[$this->alias]['password_repeat'])) 
  {
    $passwordHasher = new BlowfishPasswordHasher();
    $this->data[$this->alias]['password'] = $passwordHasher->hash
    (
      $this->data[$this->alias]['password']
      );
    $this->data[$this->alias]['password_repeat'] = $passwordHasher->hash
    (
      $this->data[$this->alias]['password_repeat']
      );
  }

  return true;
}

public function checkemail()
{
  return ($this->find('count', array('conditions' => array('email' => $this->data['User']['email']))) == 0);
}



protected $_schema = array (
  'name' => array (
    'type' => 'string',
    'null' => 'false' ,
    'default' => null
    ),                 
  'email' => array (
    'type' => 'string',
    'null' => 'false' ,
    'default' => null
    ),
  'password' => array (
    'type' => 'string',
    'null' => 'false' ,
    'default' => null
    ),
  'verified' => array (
    'type' => 'boolean' ,
    'null' => 'false' ,
    'default' => false
    )
  );
}

?>
