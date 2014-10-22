<?php
abstract class Controller_Admin_Abstract extends  Controller_Abstract{
	protected $_guestAllowedActions = array();
	
	protected function _init() {
		parent::_init();
		$this->layout->setLayout('admin');	
		if(!$this->_isLoggedIn() && !in_array($this->action,$this->_guestAllowedActions)){
			$this->redirect('admin.php?controller=index&action=login');
		}	
		
		$this->view->admin = $this->_session->getData('admin');
	}
	
	
	protected function _getRenderScript(){
		$class = get_class ( $this );
		$controller = str_replace ( 'Controller_', '', $class );
	
		if (empty ( $this->renderScript )) {
			$this->renderScript = strtolower ( str_replace('_','/',$controller )) . '/' . strtolower ( $this->action ) . '.phtml';
		}
	
		return $this->renderScript;
	}
	
	
	protected function _isLoggedIn(){
		return $this->_session->getData('admin') && $this->_session->getData('admin')->getId();
	}
}