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

class BookProduct extends ShopProduct {
	public function getPrice() {
		$price = $this->price - $this->discount ;
		if($price<0){
			throw new Exception("Product price can not be less than 0.");
		}
		echo 2;
		return $price;
	}
}

$book = new BookProduct ('PHP for beginner',-1);

try{
	echo $book->getPrice();
}catch(Exception $e){
	echo $e->getMessage();
}

echo '<br/>';
echo 1;
