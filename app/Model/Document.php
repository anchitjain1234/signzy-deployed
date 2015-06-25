<?php

App::uses('CakeEmail', 'Network/Email');

class Document extends AppModel {


    var $mongoSchema = array(
        'name' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'status' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => "0"
        ),
        'originalname' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'size' => array(
            'type' => 'integer',
            'null' => 'false',
            'default' => "0"
        ),
        'type' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'ownerid' => array(
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

}

?>
