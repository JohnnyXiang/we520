<?php
abstract class Controller_Abstract {
	protected $action = 'index';
	protected $latyout;
	protected $view;
	protected $content;
	protected $renderLayout = true;
	protected $autoRender = true;
	protected $renderScript = null;
	protected $_session;
	
	/**
	 * constructor
	 * 
	 * @param string $action        	
	 */
	public function __construct($action = 'index') {
		$this->action = $action;
		$this->view = Zend_Registry::get ( 'view' );
		$this->layout = Zend_Registry::get ( 'layout' );
		$this->view->pageTitle = DEFAULT_PAGE_TITLE;
		$this->_session = $this->getModel ( 'Session' );
		$this->view->session = $this->_session;
	}
	
	/*
	 * initial function, call before action been proccessed
	 */
	protected function _init() {
	}
	
	/*
	 * callback function, call before view be rendered
	 */
	protected function _beforeRender() {
		$this->view->session = $this->_session;
	}
	
	/*
	 * dispach to the right action
	 */
	protected function _dispatcher() {
		if (empty ( $_GET ['action'] )) {
			return $this->action = 'index';
		} else {
			
			if (method_exists ( $this, $_GET ['action'] . 'Action' )) {
				$this->action = $_GET ['action'];
			} else {
				$this->error ( 'Action ' . $_GET ['action'] . ' is not found.' );
			}
		}
	}
	
	/**
	 * handle error
	 * 
	 * @param string $message        	
	 */
	public function error($message) {
		$this->view->message = $message;
		$this->content = $this->view->render ( 'errors/index.phtml' );
		$this->layout->content = $this->content;
		echo $this->layout->render ();
		die ();
	}
	
	/**
	 * flash message
	 * 
	 * @param string $message        	
	 * @param string $type        	
	 */
	function flashMessage($message, $type = 'success') {
		$_SESSION ['flashMessage'] [$type] [] = $message;
	}
	
	/*
	 * default action
	 */
	public function indexAction() {
		$this->content = '';
	}
	
	/**
	 * run the action method
	 */
	protected function _process() {
		try {
			$this->_dispatcher ();
			$this->_init ();
			$this->{$this->action . 'Action'} ();
			$this->_beforeRender ();
		} catch ( Exception $e ) {
			echo $e->getMessage ();
			// $this->indexAction ();
		}
	}
	protected function _getRenderScript() {
		$class = get_class ( $this );
		$controller = str_replace ( 'Controller_', '', $class );
		
		if (empty ( $this->renderScript )) {
			$this->renderScript = strtolower ( $controller ) . '/' . strtolower ( $this->action ) . '.phtml';
		}
		
		return $this->renderScript;
	}
	/**
	 * render view, return response
	 * 
	 * @param number $print        	
	 */
	public function render($print = 1) {
		try {
			$this->_process ();			
			if ($this->autoRender) {
				if (empty ( $this->content )) {
					
					try {
						
						$this->content = $this->view->render ( $this->_getRenderScript () );
					} catch ( Exception $e ) {
						$this->content = '';
					}
				}
				
				if ($this->renderLayout) {
					$this->layout->content = $this->content;
					
					if ($print) {
						echo $this->layout->render ();
					} else {
						return $this->layout->render ();
					}
				} else {
					if ($print) {
						echo $this->content;
					} else {
						return $this->content;
					}
				}
			}
		} catch ( Exception $e ) {
			$this->error ( $e->getMessage () );
		}
	}
	protected function disableRender() {
		$this->autoRender = false;
	}
	protected function disableLayout() {
		$this->renderLayout = false;
	}
	protected function encodeJson($data, $keepLayouts = false) {
		$data = json_encode ( $data );
		
		return $data;
	}
	protected function renderJson($data) {
		$this->autoRender = false;
		
		$data = $this->encodeJson ( $data );
		header ( 'Content-type: application/json' );
		echo $data;
	}
	protected function redirect($url) {
		$this->_beforeRender ();
		header ( 'Location: ' . $url );
		exit ();
	}
	protected function redirectReferer() {
		$url = $_SERVER ['HTTP_REFERER'];
		IF (EMPTY ( $url )) {
			$url = $this->baseUrl ();
		}
		$this->redirect ( $url );
		// header ( 'Location: ' . $url );
		// exit ();
	}
	protected function isAjax() {
		if (! empty ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') {
			return true;
		}
		return false;
	}
	function getModel($modelClass = '', array $arguments = array()) {
		return App::getSingleton ( $modelClass, $arguments );
	}
	function getDbTableModel($modelClass = '', array $arguments = array()) {
		return App::getDbTableModel ( $modelClass, $arguments );
	}
	function getSession() {
		if (! $this->_session) {
			$this->_session = $this->getModel ( 'Session' );
		}
		
		return $this->_session;
	}
}