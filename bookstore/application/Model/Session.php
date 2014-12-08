<?php
class Model_Session extends Object{
	protected $_session;
	protected $_user;
	protected $_reloadUser = false;

	function __construct(){
		
		if(empty($namespance)){
			$namespance='Default';
		}
		
		$sessionId = App::getParam ( 'sessionId' );
		if (! empty ( $sessionId ) && !$this->isSessionStarted()) {
			session_id($sessionId);
		}		
		
		if(!$this->isSessionStarted()){
			session_start();
		}
		
		
		$this->_session =  &$_SESSION;

	}

	function getSession(){
		return $this->_session;
	}

	function getData($index){
		return $this->_session[$index];
	}
	
	function setData($key,$value){
		return $this->_session[$key] = $value;
	}
	
	public function unsetData($index=null){
		unset($this->_session[$index]);
	}
	
	
	function isSessionStarted(){
		if(function_exists('session_status')){
			return session_status()==PHP_SESSION_ACTIVE;
		}
		
		return session_id()!='';
	}

}