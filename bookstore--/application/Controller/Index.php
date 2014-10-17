<?php
class Controller_Index extends Controller_Vc {

	public function __construct($view, $layout) {
		$this->dispatcher ();
		parent::__construct ( $view, $layout, $this->action );
		$this->init ();

	}

	public function dispatcher() {
		parent::dispatcher ();
	}

	function init() {
		
	}

	function indexAction() {
		
	}
	


}