<?php
class Controller_Vc {
	public $action = 'index';
	public $latyout;
	public $view;
	public $content;
	public $renderLayout = true;
	public $autoRender = true;
	protected $renderScript = null;
	protected $session;
	
	public function __construct($action = 'index') {		
		$this->action = $action;
		$this->view = Zend_Registry::get('view');
		$this->layout = Zend_Registry::get('layout');	
	}
	
	function init(){
		
	}
	public function beforeRender() {
	
	}
	public function dispatcher() {
		
		if (empty ( $_GET ['action'] )) {
			return $this->action = 'index';
		} else {
			
			if (method_exists ( $this, $_GET ['action'] . 'Action' )) {
				$this->action = $_GET ['action'];
			} else {
				$this->action = 'index';
			}
		
		}
	
	}
	
	public function error($message) {
		$this->view->message = $message;
		$this->content = $this->view->render ( 'errors/index.phtml' );
		$this->layout->content = $this->content;
		echo $this->layout->render ();
		die ();
	}
	
	function flashMessage($message, $type = 'note') {
		$_SESSION ['flashMessage'] [$type] [] = $message;
	}
	public function indexAction() {
		$this->content = '';
	}
	function process() {
		
		try {
			$this->init();
			$this->{$this->action . 'Action'} ();
			
			$this->beforeRender ();
		} catch ( Exception $e ) {
			echo $e->getMessage ();
			//$this->indexAction ();
		}
	}
	
	public function render($print = 1) {
		
		$this->process ();
		
		if ($this->autoRender) {
			if (empty ( $this->content )) {
				$class = get_class ( $this );
				$controller = str_replace ( 'Controller_', '', $class );
				try {
					if (empty ( $this->renderScript )) {
						$this->renderScript = strtolower ( $controller ) . '/' . strtolower ( $this->action ) . '.phtml';
					}
					$this->content = $this->view->render ( $this->renderScript );
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
	}
	
	function disableRender() {
		$this->autoRender = false;
	}
	function disableLayout() {
		$this->renderLayout = false;
	}
	
	function canDo($resources, $is_own) {
		if ($this->view->canDo ( $resources, $is_own )) {
			return true;
		} else {
			$this->error ( 'No permission.' );
		}
	}
	
	public function encodeJson($data, $keepLayouts = false) {
		
		$data = json_encode ( $data );
		
		return $data;
	}
	
	function renderJson($data) {
		$this->autoRender = false;
		
		$data = $this->encodeJson ( $data );
		header ( 'Content-type: application/json' );
		echo $data;
	}
	
	
	function redirect($url) {
		$this->beforeRender ();
		header ( 'Location: ' . $url );
		exit ();
	}
	
	function redirectReferer() {
		$url = $_SERVER ['HTTP_REFERER'];
		IF (EMPTY ( $url )) {
			$url = $this->baseUrl ();
		}
		$this->redirect ( $url );
		//		header ( 'Location: ' . $url );
	//		exit ();
	}
	

	function isAjax() {
		if (! empty ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') {
			return true;
		}
		return false;
	}
	
}