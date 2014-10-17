<?php
// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) ));

set_include_path(implode(PATH_SEPARATOR, array(
APPLICATION_PATH.'/lib',
APPLICATION_PATH,
get_include_path(),
)));

function __autoload($className) {
    $extensions = array(".php");
    $paths = explode(PATH_SEPARATOR, get_include_path());
    $className = str_replace("_" , DIRECTORY_SEPARATOR, $className);
    foreach ($paths as $path) {
        $filename = $path . DIRECTORY_SEPARATOR . $className;
        foreach ($extensions as $ext) {      
            if (is_readable($filename . $ext)) {
                require_once $filename . $ext;
                break;
           }
       }
    }
}

$database = new Db(array(
	'database_type' => 'mysql',
	'database_name' => 'we520_bookstore',
	'server' => 'localhost',
	'username' => 'root',
	'password' => '',
	)
);
 
Registry::set('db',$database);

$book = new Model_Book();

//$book->insert(array('title'=>'PHP For Beginner',
//'description'=>'coming soon...',
//'isbn'=>'12333333333',
//'price'=>43,
//'status'=>1,
//'author'=>'Test Author')
//);
var_dump($book->getById(0));






