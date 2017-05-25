<?php

// ftp updater param ***************************************************************************************************
$ftp_server = 'ftp2.h2lsoft.com';
$ftp_user = 'anonymous';
$ftp_pwd = 'user@net2ftp.com';
$ftp_port = 21;
$ftp_timeout = 90;
$ftp_dir = '/hlsoft/nuts-updater';

// your parameters
$system_correct_user = ""; // put your correct login to change user owner (empty string no chown => use this for acl)
$chmod_default = 0; // chmod for mkdir put 0 for no chmod or 0770 (use this for acl) ?
$debug_mode = true; // just checking no db execute and no file copied

