<?php
class ShopProduct {
	private $_title = "default product";
	public $price = 0;
	
	public function getTitle(){
		return $this->_title;
	}
	
	private function _setTitle($title){
		$this->_title = $title;
		return $this;
	}
	
	public function setTitle($title){
		return $this->_setTitle($title);
	}
}

$product1 = new ShopProduct();
$product2 = new ShopProduct();
print $product1->getTitle();
echo '<br/>';
print $product2->getTitle();

echo '<br/>';
echo $product1->setTitle('T-shirt')->getTitle();
//echo $product1->_setTitle('T-shirt')->getTitle();
