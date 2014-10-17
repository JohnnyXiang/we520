<?php
// Define path to application directory
defined ( 'APPLICATION_PATH' ) || define ( 'APPLICATION_PATH', realpath ( dirname ( __FILE__ ) ) );
define ( 'BASEURL', '/we520/bookstore' );

// Ensure library/ is on include_path
set_include_path ( implode ( PATH_SEPARATOR, array (
		realpath ( APPLICATION_PATH . '/application' ),
		realpath ( APPLICATION_PATH . '/library' ),
		APPLICATION_PATH,
		get_include_path () 
) ) );

require_once 'Bootstrap.php';
$bootstrap = new Bootstrap();
$bootstrap->run();

