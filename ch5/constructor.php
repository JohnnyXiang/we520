<?php
class ShopProduct {
	protected $_title;
	public $price = 0;
	
	public function __construct( $title,$price ) {
		$this->_title = $title;
		$this->price = $price;
	}
	
	function getTitle() {
		return $this->_title;
	}
}

$product1 = new ShopProduct( "T-Shirt", 5.99 );

echo $product1->getTitle();

echo '<br/>';
$product2 = new ShopProduct( "Pans", 6.99 );

echo $product2->getTitle();