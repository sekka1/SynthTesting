<?php

error_reporting( E_ALL | E_STRICT );
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Los_Angeles');

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
//$filecontents = file_get_contents("../../main/php/public/.htaccess", "r");

/*********** IMPORTANT: The environmental variable "ZEND_APPLICATION_ENV" in the shell is used to determine what server we are on - MRR 20120910 */
if (getenv("ZEND_APPLICATION_ENV")) {
    define('APPLICATION_ENV', getenv("ZEND_APPLICATION_ENV"));    
} else {
    define('APPLICATION_ENV', 'staging');
}

define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
define('TESTS_PATH', realpath(dirname(__FILE__)));

$includePaths = array(LIBRARY_PATH, get_include_path());
set_include_path( implode( PATH_SEPARATOR, array( realpath( APPLICATION_PATH . '/../library'), realpath( APPLICATION_PATH . '/../library/AlgorithmsIO'), realpath( APPLICATION_PATH . '/../library/AlgorithmsIO/classes'), realpath( APPLICATION_PATH . '/../library/AlgorithmsIO/classes/Amazon_SDK/sdk-1.5.3/utilities'), get_include_path(), APPLICATION_PATH . '/../application/userApp/', APPLICATION_PATH . '/../application/models' ) ) );

require_once "Zend/Loader.php";
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);
