<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * First create the bucket in S3 manually and entre the name of bucket here.
 */
$config['s3_bucket_name'] = 'documentuploads';

$config['email_queue'] = 'deployement_emails';
$config['upload_queue'] = 'deployement_uploads';

$config['doc_owner'] = '-1';
$config['doc_pending'] = '0';
$config['doc_completed'] = '1';
$config['doc_void'] = '2';
$config['doc_rejected'] = '3';
$config['user_verified'] = 1;
$config['user_not_verified'] = 0;
$config['upload_location_url'] = '/home/ubuntu/uploads/';
$config['legal_head'] = '1';
$config['auth_sign'] = '2';
$config['unauth_sign'] = '0';
$config['rejected_sign'] = '-1';
