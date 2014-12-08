<?php
class ShopProduct {
	const AVAILABLE = 0;
	const OUT_OF_STOCK = 1;
	private $_qty = 0;
	
	function setQty($qty){
		$this->_qty = $qty;
	}
	
	function getQty(){
		return $this->_qty;
	}
	
	function getAvailability(){
		if($this->getQty()<=0){
			return self::OUT_OF_STOCK;
		}
		
		return self::AVAILABLE;
	}
	
}

$product = new ShopProduct();

$product ->setQty(1);

if($product->getAvailability() == ShopProduct ::OUT_OF_STOCK  ){
	echo 'Sorry, product is currently out of stock.';
}else{
	echo 'Available';
}
