<?php

class Profile extends AppModel
{
    public $validate = array(
        'uid' => array(
            'isUnique' => array(
                'rule' => array('checkid'),
                'message' => 'User is already having profile.'
            )
        ),
    );
    
    public function checkid() {
        return ($this->find('count', array('conditions' => array('uid' => $this->data['Profile']['uid']))) == 0);
    }
    
    var $mongoSchema = array(
        'uid' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'verified' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => "-2"
        ),
        'verificationimage' => array(
            'type' => 'string',
            'null' => 'true',
            'default' => null
        ),
        'profilepicture' => array(
            'type' => 'string',
            'null' => 'true',
            'default' => null
        ),
        'cardnumber' => array(
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
