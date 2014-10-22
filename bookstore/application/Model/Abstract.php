<?php
abstract class Model_Abstract extends Zend_Db_Table_Row_Abstract {
	protected $_tbModel;
	protected $_resourceModel;
	/**
	 * Object attributes
	 *
	 * @var array
	 */
	protected $_xdata = array();
	
	/**
	 * Name of object id field
	 *
	 * @var string
	 */
	protected $_idFieldName = null;
	
	/**
	 * Setter/Getter underscore transformation cache
	 *
	 * @var array
	 */
	protected static $_underscoreCache = array();
	
	/**
	 * Get value from _data array without parse key
	 *
	 * @param string $key        	
	 * @return mixed
	 */
	protected function _getData($key) {
		return isset ( $this->_data [$key] ) ? $this->_data [$key] : null;
	}
	
	function _getResource() {
		if ($this->_resourceModel) {
			return $this->_resourceModel;
		}
		
		$this->_resourceModel = new $this->_tbModel ();
		
		return $this->_resourceModel;
	}
	
	function load($id = null, $field = null) {
		$this->_getResource ()->load ( $this, $id,'*', $field?$field:$this->_idFieldName );
		return $this;
	}
	
	/**
	 * set name of object id field
	 *
	 * @param string $name        	
	 * @return Varien_Object
	 */
	public function setIdFieldName($name) {
		$this->_idFieldName = $name;
		return $this;
	}
	
	/**
	 * Retrieve name of object id field
	 *
	 * @param string $name        	
	 * @return Varien_Object
	 */
	public function getIdFieldName() {
		return $this->_idFieldName;
	}
	
	/**
	 * Retrieve object id
	 *
	 * @return mixed
	 */
	public function getId() {
		if ($this->getIdFieldName ()) {
			return $this->_getData ( $this->getIdFieldName () );
		}
		return $this->_getData ( 'id' );
	}
	
	/**
	 * Set object id field value
	 *
	 * @param mixed $value        	
	 * @return Varien_Object
	 */
	public function setId($value) {
		if ($this->getIdFieldName ()) {
			$this->setData ( $this->getIdFieldName (), $value );
		} else {
			$this->setData ( 'id', $value );
		}
		return $this;
	}
	
	/**
	 * Add data to the object.
	 *
	 * Retains previous data in the object.
	 *
	 * @param array $arr        	
	 * @return Varien_Object
	 */
	public function addData(array $arr) {
		foreach ( $arr as $index => $value ) {
			$this->setData ( $index, $value );
		}
		return $this;
	}
	
	/**
	 * Overwrite data in the object.
	 *
	 * $key can be string or array.
	 * If $key is string, the attribute value will be overwritten by $value
	 *
	 * If $key is an array, it will overwrite all the data in the object.
	 *
	 * @param string|array $key        	
	 * @param mixed $value        	
	 * @return Varien_Object
	 */
	public function setData($key, $value = null) {
		try{
			parent::__set($key, $value);
		}catch(Exception $e){
			$this->_data [$key] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Unset data from the object.
	 *
	 * $key can be a string only. Array will be ignored.
	 *
	 * @param string $key        	
	 * @return Varien_Object
	 */
	public function unsetData($key = null) {
		unset ( $this->_data [$key] );
		
		return $this;
	}
	
	/**
	 * Retrieves data from the object
	 *
	 * If $key is empty will return all the data as an array
	 * Otherwise it will return value of the attribute specified by $key
	 *
	 * If $index is specified it will assume that attribute data is an array
	 * and retrieve corresponding member.
	 *
	 * @param string $key        	
	 * @param string|int $index        	
	 * @return mixed
	 */
	public function getData($key = '', $index = null) {
		if ('' === $key) {
			return $this->_data;
		}
		
		$default = null;
		
		// accept a/b/c as ['a']['b']['c']
		if (strpos ( $key, '/' )) {
			$keyArr = explode ( '/', $key );
			$data = $this->_data;
			foreach ( $keyArr as $i => $k ) {
				if ($k === '') {
					return $default;
				}
				if (is_array ( $data )) {
					if (! isset ( $data [$k] )) {
						return $default;
					}
					$data = $data [$k];
				} else {
					return $default;
				}
			}
			return $data;
		}
		
		// legacy functionality for $index
		if (isset ( $this->_data [$key] )) {
			if (is_null ( $index )) {
				return $this->_data [$key];
			}
			
			$value = $this->_data [$key];
			if (is_array ( $value )) {
				// if (isset($value[$index]) && (!empty($value[$index]) || strlen($value[$index]) > 0)) {
				/**
				 * If we have any data, even if it empty - we should use it, anyway
				 */
				if (isset ( $value [$index] )) {
					return $value [$index];
				}
				return null;
			} elseif (is_string ( $value )) {
				$arr = explode ( "\n", $value );
				return (isset ( $arr [$index] ) && (! empty ( $arr [$index] ) || strlen ( $arr [$index] ) > 0)) ? $arr [$index] : null;
			}
			return $default;
		}
		return $default;
	}
	
	/**
	 * Public wrapper for __toString
	 *
	 * Will use $format as an template and substitute {{key}} for attributes
	 *
	 * @param string $format        	
	 * @return string
	 */
	public function toString($format = '') {
		if (empty ( $format )) {
			$str = implode ( ', ', $this->getData () );
		} else {
			preg_match_all ( '/\{\{([a-z0-9_]+)\}\}/is', $format, $matches );
			foreach ( $matches [1] as $var ) {
				$format = str_replace ( '{{' . $var . '}}', $this->getData ( $var ), $format );
			}
			$str = $format;
		}
		return $str;
	}
	
	/**
	 * Set/Get attribute wrapper
	 *
	 * @param string $method        	
	 * @param array $args        	
	 * @return mixed
	 */
	public function __call($method, $args) {
		switch (substr ( $method, 0, 3 )) {
			case 'get' :
				// Varien_Profiler::start('GETTER: '.get_class($this).'::'.$method);
				$key = $this->_underscore ( substr ( $method, 3 ) );
				$data = $this->getData ( $key, isset ( $args [0] ) ? $args [0] : null );
				// Varien_Profiler::stop('GETTER: '.get_class($this).'::'.$method);
				return $data;
			
			
			case 'uns' :
				// Varien_Profiler::start('UNS: '.get_class($this).'::'.$method);
				$key = $this->_underscore ( substr ( $method, 3 ) );
				$result = $this->unsetData ( $key );
				// Varien_Profiler::stop('UNS: '.get_class($this).'::'.$method);
				return $result;
			
			case 'has' :
				// Varien_Profiler::start('HAS: '.get_class($this).'::'.$method);
				$key = $this->_underscore ( substr ( $method, 3 ) );
				// Varien_Profiler::stop('HAS: '.get_class($this).'::'.$method);
				return isset ( $this->_data [$key] );
		}
		throw new Zend_Exception ( "Invalid method " . get_class ( $this ) . "::" . $method . "(" . print_r ( $args, 1 ) . ")" );
	}
	
	/**
	 * Attribute getter (deprecated)
	 *
	 * @param string $var        	
	 * @return mixed
	 */
	public function __get($var) {
		$var = $this->_underscore ( $var );
		return $this->getData ( $var );
	}
	
	
	
	/**
	 * checks whether the object is empty
	 *
	 * @return boolean
	 */
	public function isEmpty() {
		if (empty ( $this->_data )) {
			return true;
		}
		return false;
	}
	
	/**
	 * Converts field names for setters and geters
	 *
	 * $this->setMyField($value) === $this->setData('my_field', $value)
	 * Uses cache to eliminate unneccessary preg_replace
	 *
	 * @param string $name        	
	 * @return string
	 */
	protected function _underscore($name) {
		if (isset ( self::$_underscoreCache [$name] )) {
			return self::$_underscoreCache [$name];
		}
		// arien_Profiler::start('underscore');
		$result = strtolower ( preg_replace ( '/(.)([A-Z])/', "$1_$2", $name ) );
		// arien_Profiler::stop('underscore');
		self::$_underscoreCache [$name] = $result;
		return $result;
	}
	protected function _camelize($name) {
		return uc_words ( $name, '' );
	}
	
	/**
	 * serialize object attributes
	 *
	 * @param array $attributes        	
	 * @param string $valueSeparator        	
	 * @param string $fieldSeparator        	
	 * @param string $quote        	
	 * @return string
	 */
	public function serialize($attributes = array(), $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"') {
		$res = '';
		$data = array ();
		if (empty ( $attributes )) {
			$attributes = array_keys ( $this->_data );
		}
		
		foreach ( $this->_data as $key => $value ) {
			if (in_array ( $key, $attributes )) {
				$data [] = $key . $valueSeparator . $quote . $value . $quote;
			}
		}
		$res = implode ( $fieldSeparator, $data );
		return $res;
	}
}