<?php 
final class App {
	
	static $_params =array();
	/**
	 * Retrieve model object singleton
	 *
	 * @param string $modelClass        	
	 * @param array $arguments        	
	 * @return Mage_Core_Model_Abstract
	 */
	public static function getSingleton($modelClass = '', array $arguments = array()) {
		$registryKey = '_singleton/' . $modelClass;
		if (! Zend_Registry::isRegistered ( $registryKey )) {
			Zend_Registry::set ( $registryKey, self::getModel ( $modelClass, $arguments ) );
		}
		return Zend_Registry::get ( $registryKey );
	}
	
	public static function getModel($modelClass = '', $arguments = array()) {
		$className = self::getModelClassName ( $modelClass );
		
		if (class_exists ( $className )) {
			$obj = new $className ( $arguments );
			return $obj;
		} else {
			return false;
		}
	}
	
	public static function getResourceSingleton($modelClass = '', $arguments = array()) {
		$registryKey = '_singleton/resource_' . $modelClass;
		if (! Zend_Registry::isRegistered ( $registryKey )) {
			Zend_Registry::set ( $registryKey, self::getResourceModel ( $modelClass, $arguments ) );
		}
		return Zend_Registry::get ( $registryKey );
	}
	
	public static function getDbTableModel($modelClass = '', $arguments = array()) {
		$className = self::getResourceModelClassName ( $modelClass );
		
		if (class_exists ( $className )) {
			$obj = new $className ( $arguments );
			return $obj;
		} else {
			return false;
		}
	}
	
	public static function getResourceModelClassName($modelClass) {
		return $class_name = 'Model_DbTable_' . Inflector::camelize ( Inflector::underscore ( $modelClass ) );
	}
	
	public static function getModelClassName($modelClass) {
		return $class_name = 'Model_' . Inflector::camelize ( Inflector::underscore ( $modelClass ) );
	}
	
 	/**
     * Set a userland parameter
     *
     * Uses $key to set a userland parameter. If $key is an alias, the actual
     * key will be retrieved and used to set the parameter.
     *
     * @param mixed $key
     * @param mixed $value
     * @return null
     */
    public static function setParam($key, $value)
    {
        self::$_params[$key] = $value;        
    }

    /**
     * Retrieve a parameter
     *
     * Retrieves a parameter from the instance. Priority is in the order of
     * userland parameters (see {@link setParam()}), $_GET, $_POST. If a
     * parameter matching the $key is not found, null is returned.
     *
     * If the $key is an alias, the actual key aliased will be used.
     *
     * @param mixed $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        
        if (isset(self::$_params[$key])) {
            return self::$_params[$key];
        } elseif (isset($_GET[$key])) {
            return $_GET[$key];
        } elseif (isset($_POST[$key])) {
            return $_POST[$key];
        }

        return $default;
    }

    /**
     * Retrieve an array of parameters
     *
     * Retrieves a merged array of parameters, with precedence of userland
     * params (see {@link setParam()}), $_GET, $_POST (i.e., values in the
     * userland params will take precedence over all others).
     *
     * @return array
     */
    public function getParams()
    {
        $return       = self::$_params;
        
        if (isset($_GET)
            && is_array($_GET)
        ) {
            $return  = array_merge($return,$_GET);
        }
        if (isset($_POST)
            && is_array($_POST)
        ) {
            $return  = array_merge($return,$_POST);
        }
        return $return;
    }

    /**
     * Set parameters
     *
     * Set one or more parameters. Parameters are set as userland parameters,
     * using the keys specified in the array.
     *
     * @param array $params
     * @return null
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            self::setParam($key, $value);
        }
       
    }
    
    public function isRequestPost(){
    	return isset($_POST) && !empty($_POST);
    }
	

}