<?php 
final class App {
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
	

}