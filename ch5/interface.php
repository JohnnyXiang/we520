<?php
interface Chargeable {
	public function getPrice();
}

abstract class ShopProduct {
	const AVAILABLE = 0;
	const OUT_OF_STOCK = 1;
}

class BookProduct extends ShopProduct  implements Chargeable{
	public function getPrice() {
	return ( $this->price - $this->discount );
	}
}

new BookProduct();
