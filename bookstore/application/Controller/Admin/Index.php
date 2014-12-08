<?php
class Controller_Admin_Index extends Controller_Admin_Abstract {	

	
	protected $_guestAllowedActions = array('login');
	
	
	function indexAction() {
		
	}
	
	function loginAction(){
		$this->layout->setLayout('admin-login');
		
		if(App::isRequestPost()){
			try{
				$username  =App::getParam('username');
				$password  =App::getParam('password');
				
				if(empty($username) || empty($password)){
					 throw new Exception('Please enter both your username and password.');
				}
				
				if($admin = $this->getDbTableModel('admin')->verifyLogin($username,$password)){
					$this->_session->setData('admin',$admin);
					$this->redirect('admin.php');
				}
				
				throw new Exception('Username and password are invalid, please try again.');
				
			}catch(Exception $e){
				$this->flashMessage($e->getMessage(),'danger');
			}
		}
	}
	
	
	function logoutAction(){
		$this->_session->unsetData('admin');
		
		$this->flashMessage('You have been logged out successfully.');
		$this->redirect('admin.php');
	}
	
}