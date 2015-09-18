<?php

class Col extends AppModel {

    var $mongoSchema = array(
        'did' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'uid' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => null
        ),
        'status' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => '0'
        ),
        'token' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => '0'
        ),
        'cid' => array(
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
        ),
        'photoverification' => array(
            'type' => 'string',
            'null' => 'false',
            'default' => '-1'
        ),
        'photocaptured' => array(
            'type' => 'string',
            'null' => 'true',
            'default' => null
        )
    );

}
