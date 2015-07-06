<?php

class Company extends AppModel {

    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter company name'
            ),
            'isUnique' => array(
                'rule' => array('checkname'),
                'message' => 'Company is already registered.Please contact company legal head to authorise you.'
            )
        )
    );
    var $mongoSchema = array(
        'name' => array(
            'type' => 'string',
            'null' => 'false'
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
    
    public function checkname() {
        $count_check = $this->find('count', array('conditions' => array('name' => new MongoRegex('/^'.$this->data['Company']['name'] . "$/i"))));
        
        if($count_check === 0)
        {
            return true;
        }
        return false;
    }

}
