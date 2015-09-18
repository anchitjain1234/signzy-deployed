<?php

class Compmember extends AppModel{
    
    var $mongoSchema = array(
        'uid' => array(
            'type' => 'string',
            'null' => 'false'
        ),
        'cid' => array(
            'type' => 'string' ,
            'null' => 'false'
        ),
        'status' => array(
            'type' => 'string' ,
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
