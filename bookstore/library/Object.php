<?php

/**
 * App default Object
 *
 */
class Object 
{

    /**
     * Object attributes
     *
     * @var array
     */
    protected $_data = array();

   
    /**
     * Converts field names for setters and geters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unneccessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }
        #Vc_Profiler::start('underscore');
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        #Vc_Profiler::stop('underscore');
        self::$_underscoreCache[$name] = $result;
        return $result;
    }

    protected function _camelize($name)
    {
        return uc_words($name, '');
    }

    /**
     * serialize object attributes
     *
     * @param   array $attributes
     * @param   string $valueSeparator
     * @param   string $fieldSeparator
     * @param   string $quote
     * @return  string
     */
    public function serialize($attributes = array(), $valueSeparator='=', $fieldSeparator=' ', $quote='"')
    {
        $res  = '';
        $data = array();
        if (empty($attributes)) {
            $attributes = array_keys($this->_data);
        }

        foreach ($this->_data as $key => $value) {
            if (in_array($key, $attributes)) {
                $data[] = $key . $valueSeparator . $quote . $value . $quote;
            }
        }
        $res = implode($fieldSeparator, $data);
        return $res;
    }

    /**
     * Get value from _data array without parse key
     *
     * @param   string $key
     * @return  mixed
     */
    protected function _getData($key)
    {
    	return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }
    
    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $arr
     * @return Varien_Object
     */
    public function addData(array $arr)
    {
    	foreach($arr as $index=>$value) {
    		$this->setData($index, $value);
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
    public function setData($key, $value=null)
    {
    
    	$this->_data[$key] = $value;
    		
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
    public function unsetData($key=null)
    {
    
    	unset($this->_data[$key]);
    		
    
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
    public function getData($key='', $index=null)
    {
    	if (''===$key) {
    		return $this->_data;
    	}
    
    	$default = null;
    
    	// accept a/b/c as ['a']['b']['c']
    	if (strpos($key,'/')) {
    		$keyArr = explode('/', $key);
    		$data = $this->_data;
    		foreach ($keyArr as $i=>$k) {
    			if ($k==='') {
    				return $default;
    			}
    			if (is_array($data)) {
    				if (!isset($data[$k])) {
    					return $default;
    				}
    				$data = $data[$k];
    			}else {
    				return $default;
    			}
    		}
    		return $data;
    	}
    
    	// legacy functionality for $index
    	if (isset($this->_data[$key])) {
    		if (is_null($index)) {
    			return $this->_data[$key];
    		}
    
    		$value = $this->_data[$key];
    		if (is_array($value)) {
    			//if (isset($value[$index]) && (!empty($value[$index]) || strlen($value[$index]) > 0)) {
    			/**
    			 * If we have any data, even if it empty - we should use it, anyway
    			 */
    			if (isset($value[$index])) {
    				return $value[$index];
    			}
    			return null;
    		} elseif (is_string($value)) {
    			$arr = explode("\n", $value);
    			return (isset($arr[$index]) && (!empty($arr[$index]) || strlen($arr[$index]) > 0)) ? $arr[$index] : null;
    		}
    		return $default;
    	}
    	return $default;
    }
}
