<?php
class ShopProduct {
	protected $title;
	public $price;
	function __construct( $title, $price ) {
		$this->title = $title;
		$this->price = $price;
	}
	protected function getTitle() {
		return $this->title;
	}
	function getSummaryLine() {
		return $base = $this->title;
	}
}

class CdProduct extends ShopProduct {
	public $playLength;
	
	function __construct( $title, $price, $playLength ) {
		parent::__construct( $title, $price );
		$this->playLength = $playLength;
	}
	function getPlayLength() {
		return $this->playLength;
	}
	function getSummaryLine() {
		return parent::getSummaryLine(). ', playing time - '.$this->playLength;
	}
	
	function getTitle() {
		return $this->title;
	}
}

class BookProduct extends ShopProduct {
	public $numPages;
	
	function __construct( $title, $price, $numPages ) {
		parent::__construct( $title, $price );
		$this->numPages = $numPages;
	}
	function getNumberOfPages() {
		return $this->numPages;
	}
	function getSummaryLine() {
		return parent::getSummaryLine().',  page count -'.$this->numPages;
	}
	
	function getTitle() {
		return parent::getTitle();
	}
}

$cd = new CdProduct('CD Title',10,'120mins');
echo $cd ->getTitle();
echo  '<br/>';
echo $cd ->getPlayLength();
echo  '<br/>';
echo $cd ->getSummaryLine();
echo  '<br/>';

$book = new BookProduct('Book Title',10,'600');
echo $book ->getTitle();
echo  '<br/>';
echo $book ->getNumberOfPages();
echo  '<br/>';
echo $book ->getSummaryLine();
echo  '<br/>';

