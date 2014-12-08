<?php
Class View{
	protected $_basePath=nulll;
	
	function setBasePath($path){
		$this->_basePath = $path;
		return $this;
	}
	
	function getBasePath(){
		return $this->_basePath;
	}
	
	function render($renderScript){
		// find the script file name using the parent private method
		$this->_file = $this->_script($renderScript);
		
		unset($name); // remove $name from local scope
		
		ob_start();
		$this->_run($this->_file);
		
		return ob_get_clean(); 
	}
	
	
	/**
	 * Finds a view script from the available directories.
	 *
	 * @param string $name The base name of the script.
	 * @return void
	 */
	protected function _script($name)
	{
		
	
		if ($this->_basePath==null) {		
			throw new Exception('no view script directory set; unable to determine location for view script');			 
		}
		
	
	
		if (is_readable($this->_basePath .'/scripts/'. $name)) {
				return $this->_basePath  .'/scripts/'. $name;
		}
		
		if (is_readable($this->_basePath .'/'. $name)) {
			return $this->_basePath  .'/'. $name;
		}
	
		
		$message = "script '$name' not found in path ("	. $this->_basePath	. ")";
		throw new Exception($message);	
	}
	
	/**
	 * Includes the view script in a scope with only public $this variables.
	 *
	 * @param string The view script to execute.
	 */
	protected function _run()
	{
		
		include func_get_arg(0);
		
	}
}