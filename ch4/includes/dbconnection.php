<?php 
/*
define database constant 
*/
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASSWORD','');
define('DB_DATABASE','we520');
define('ROOT_DIR',realpath(dirname(dirname(__FILE__))));

/***
connect to the database server
*/
$con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die('Can not connect to database server');

/**
select the database
*/
mysql_select_db(DB_DATABASE);







