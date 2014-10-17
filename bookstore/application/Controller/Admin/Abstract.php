<?php
abstract class Controller_Admin_Abstract extends  Controller_Abstract{
	
	protected function _init() {
		parent::_init();
		$this->layout->setLayout('admin');		
	}
	
	
	protected function _getRenderScript(){
		$class = get_class ( $this );
		$controller = str_replace ( 'Controller_', '', $class );
	
		if (empty ( $this->renderScript )) {
			$this->renderScript = strtolower ( str_replace('_','/',$controller )) . '/' . strtolower ( $this->action ) . '.phtml';
		}
	
		return $this->renderScript;
	}
	
}