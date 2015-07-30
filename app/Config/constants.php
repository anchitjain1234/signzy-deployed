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

/*
 * Document signing related constants.
 */
$config['doc_owner'] = '-1';
$config['doc_pending'] = '0';
$config['doc_completed'] = '1';
$config['doc_void'] = '2';
$config['doc_rejected'] = '3';
$config['photo_verified'] = '1';
$config['photo_unverified'] = '0';
$config['photo_not_exists'] = '-1';
$config['photo_rejected'] = '-2';
/*
 * Email verification related constants.
 */
$config['user_verified'] = 1;
$config['user_not_verified'] = 0;

/*
 * Location related constants.
 */
$config['upload_location_url'] = '/home/ubuntu/uploads/';
$config['image_upload_location'] = '/home/ubuntu/imguploads/';
$config['python_scripts_location'] = '/home/ubuntu/pyscr/';
$config['processed_img_location'] = '/home/ubuntu/processedimg/';

/*
 * Signatory related constants.
 */
$config['legal_head'] = '1';
$config['auth_sign'] = '2';
$config['unauth_sign'] = '0';
$config['rejected_sign'] = '-1';

/*
 * Legal verification status
 */
$config['profile_verified'] = '1';
$config['profile_unverified'] = '0';
$config['profile_rejected'] = '-1';
$config['profile_not_exists'] = '-2';
/*
 * User account status
 */
$config['customer'] = '0';
$config['admin'] = '1';
$config['moderator'] = '2';
$config['support'] = '3';