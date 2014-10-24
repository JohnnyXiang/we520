<?php
class Model_Product extends Model_Abstract{
	protected $_tbModel = 'Model_DbTable_Product';
	protected $_idFieldName = 'product_id';
	
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	
	
	function saveImage($image,$position=0,$isMain=false){
		$result = $this->_getResource ()->saveImage ( $this->getId(), $image,$position );
		if($result && $isMain){
			$this->setData('main_image',$image)
			->save();
		}
		
		return $this;
	}
	
	function setCategories($categories){
		$this->_getResource ()->setCategories ( $this->getId(), $categories );
		return $this;
	}
	

}