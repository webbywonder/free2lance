<?php  if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
*/

$active_group = 'default';
$active_record = true;

use Symfony\Component\Dotenv\Dotenv;

if (file_exists('./.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/../../.env');

    $db['default']['hostname'] = getenv('DB_HOST');
    $db['default']['port'] = 8889;
    $db['default']['username'] = getenv('DB_USER');
    $db['default']['password'] = getenv('DB_PASSWORD');
    $db['default']['database'] = getenv('DB_DATABASE');
    $db['default']['dbdriver'] = 'mysqli';
    $db['default']['dbprefix'] = '';
    $db['default']['pconnect'] = false;
    $db['default']['db_debug'] = true;
    $db['default']['cache_on'] = false;
    $db['default']['cachedir'] = '';
    $db['default']['char_set'] = 'utf8';
    $db['default']['dbcollat'] = 'utf8_general_ci';
    $db['default']['swap_pre'] = '';
    $db['default']['autoinit'] = true;
    $db['default']['stricton'] = false;
} else {
    $db['default']['hostname'] = 'localhost';
    $db['default']['username'] = 'root';
    $db['default']['password'] = 'root';
    $db['default']['database'] = 'fc';
    $db['default']['port'] = 8889;
    $db['default']['dbdriver'] = 'mysqli';
    $db['default']['dbprefix'] = '';
    $db['default']['pconnect'] = false;
    $db['default']['db_debug'] = true;
    $db['default']['cache_on'] = false;
    $db['default']['cachedir'] = '';
    $db['default']['char_set'] = 'utf8';
    $db['default']['dbcollat'] = 'utf8_general_ci';
    $db['default']['swap_pre'] = '';
    $db['default']['autoinit'] = true;
    $db['default']['stricton'] = false;
}


/* End of file database.php */
/* Location: ./application/config/database.php */
