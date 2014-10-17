<?php
Class Layout{
	protected $_basePath;
	protected $_view;
	protected $_viewBasePrefix = '.phtml';
	/**
	 * Layout view
	 * @var string
	 */
	protected $_layout = 'layout';
	
	function setLayoutPath($path){
		$this->_basePath = $path;
		return $this;
	}
	
	function getLayoutPath(){
		return $this->_basePath;
	}
	
	function setLayout($layout){
		$this->_layout = $layout;
		return $this;
	}
	
	function getLayout(){
		return $this->_layout;
	}
	
	
	
	function setView(View $view){
		$this->_view = $view;
		return $this;
	}
	
	function getView(){
		return $this->_view;
	}
	
 /**
     * Render layout
     *
     * Sets internal script path as last path on script path stack, assigns
     * layout variables to view, determines layout name using inflector, and
     * renders layout view script.
     *
     * $name will be passed to the inflector as the key 'script'.
     *
     * @param  mixed $name
     * @return mixed
     */
    public function render($name = null)
    {
        if (null === $name) {
            $name = $this->getLayout();
        }

     
        $view = $this->getView();

        $view->setBasePath($this->getLayoutPath());
		
        $view->layout = $this;
        
        return $view->render($name.$this->_viewBasePrefix);
    }
	
}