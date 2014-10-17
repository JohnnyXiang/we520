<?php
class Bootstrap {
	
	function run() {
		$this->_initAutoload();
		$this->_initConfig();
		$this->_initDb();
		$this->_initView();
		$this->_initLayout();
		$this->_initDispatch();
	}
	
	protected function _initAutoload() {
		require_once ('Zend/Loader/Autoloader.php');
		$autoloader = Zend_Loader_Autoloader::getInstance ();
		$autoloader->registerNamespace ( 'Model_' );
		$autoloader->registerNamespace ( 'Controller_' );
	}
	
	protected function _initConfig() {
		$config = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', 'production' );
		$registry = Zend_Registry::getInstance ();
		$registry->set ( 'config', $config );
	}
	
	protected function _initDb() {
		 $config = Zend_Registry::get ( 'config' );
		$db = Zend_Db::factory ( $config->resources->db );
		$db->setFetchMode ( Zend_Db::FETCH_OBJ );
		
		Zend_Registry::set ( "db", $db );
		
		Zend_Db_Table_Abstract::setDefaultAdapter ( $db );
		
		// set DB to UTC timezone for this session
		switch ($config->resources->adapter) {
			case 'mysqli' :
			case 'mysql' :
			case 'pdo_mysql' :
				{
					$db->query ( "SET time_zone = '+0:00'" );
					break;
				}
			
			case 'postgresql' :
				{
					$db->query ( "SET time_zone = '+0:00'" );
					break;
				}
			
			default :
				{
					// do nothing
				}
		}
		
		// attempt to disable strict mode
		try {
			$db->query ( "SET SQL_MODE = ''" );
		} catch ( Exception $e ) {
		}
	}
	
	protected function _initView() {
		$view = new Zend_View ();
		$view->setBasePath ( APPLICATION_PATH . '/application/View/default/Views' );
		
		$prefix = 'Zend_View_Helper';
		$dir = "View/default/Views/helpers";
		$view->addHelperPath ( $dir, $prefix );
		
		Zend_Registry::set ( 'view', $view );
	}
	
	protected function _initLayout() {
		//init layout
		$layout = new Zend_Layout ();
		
		// Set a layout script path:
		$layout->setLayoutPath ( APPLICATION_PATH . '/application/View/default/Layouts' );
		$layout->setView ( Zend_Registry::get ( 'view' ) );
		Zend_Registry::set ( 'layout', $layout );
	}
	
	protected function _initDispatch() {
		foreach ( $_GET as $key => $value ) {
			$_GET [$key] = trim ( $value );
		}
		
		if (@$_GET ['controller']) {
			$controller = strtolower ( $_GET ['controller'] );
		} else {
			
			$uri = $_SERVER ['REQUEST_URI'];
			
			$parts = parse_url ( $uri );
			
			$uri = trim ( $parts ['path'] );
			
			if (BASEURL) {
				$uri = str_replace ( BASEURL, '', $uri );
			}
			$uri = str_replace ( '.php', '', $uri );
			
			$uri = strtolower ( trim ( $uri, '/\\' ) );
			
			if (empty ( $uri )) {
				$uri = 'index';
			}
			
			switch ($uri) {
				case '' :
					$controller = 'index';
					break;
				default :
					$controller = $uri;
					break;
			}
		
		}
		
		$controller = 'Controller_' . ucfirst ( $controller );
		
		if(!class_exists($controller)){
			throw new Exception('Page not found.');
		}
		
		$index = new $controller ();
		$index->render ();
	
	}
}