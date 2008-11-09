<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| System Lock
|--------------------------------------------------------------------------
|
| Set to true to override the database system lock.
|
*/
$config['system_locked'] = FALSE;


/*
|--------------------------------------------------------------------------
| Backend Base URL
|--------------------------------------------------------------------------
|
| Change this to mask the backend url.
|
*/
$config['backend_base'] = 'backend';


/*
|--------------------------------------------------------------------------
| Remember Me Salt
|--------------------------------------------------------------------------
|
| Static salt used to stretch the remember me key.
| Paranoid length? Check.
|
*/
$config['rem_salt']	= 'jKJiohPIOhioH89H78hjojkljioJ3895D9jWfcNKdkov58WZl0W7LhogYrD0oI8i4QpFGZDK2CBFBb3MuHPRiyrnbRbOk8Y';

/*
|--------------------------------------------------------------------------
| Script Request Time
|--------------------------------------------------------------------------
|
| Limits calls to time() which are 'slow'.
| PHP 5 could use $_SERVER['REQUEST_TIME']
|
*/
$config['request_time'] = time();

/* End of file access.php */
/* Location: ./system/application/config/access.php */