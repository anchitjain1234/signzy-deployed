<?php

App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {

    public $primarykey = '_id';
    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter your email addreess'
            )
        ),
        'username' => array(
            'isUnique' => array(
                'rule' => array('checkemail'),
                'message' => 'Entered email address is already registered.Please enter unique email'
            ),
            'email' => array(
                'rule' => 'email',
                'message' => 'Please give a valid email address'
            ),
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter your email addreess'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter password',
            ),
            'size' => array(
                'rule' => array('minLength', '8'),
                'message' => 'Password of minimum length of 8 characters is required'
            )
        )
    );

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash
                    (
                    $this->data[$this->alias]['password']
            );
        }

        return true;
    }

    public function checkemail() {
        return ($this->find('count', array('conditions' => array('username' => $this->data['User']['username']))) == 0);
    }

    var $mongoSchema = array(
        'name' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'username' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'password' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'verified' => array(
            'type' => 'integer',
            'null' => 'false',
            'default' => 0
        ),
        'token' => array(
            'type' => 'string',
            'null' => 'true',
            'default' => null
        ),
        'created' => array(
            'type' => 'timestamp',
            'null' => 'false'
        ),
        'modified' => array(
            'type' => 'timestamp',
            'null' => 'false'
        )
    );

}

?>
