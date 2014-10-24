<?php
Class Model_DbTable_Product extends Model_DbTable_Abstract{
	protected $_rowClass = 'Model_Product';
	protected $_name = 'products';
	protected $_imageTable = 'product_images';
	protected $_productCategoryTable = 'product_categories';
	
	function saveImage($productId,$image,$position=0){
		return $this->getDb()->insert($this->_imageTable,array(
			'product_id'=>$productId,
			'path'=>$image,
			'position'=>$position
		));
	}
	
	function setCategories($productId,$categories){
		//delete existed records
		$this->getDb()->delete($this->_productCategoryTable,$this->getDb()->quoteInto('product_id=?',$productId));
		foreach ($categories as $position=>$category){
			$this->getDb()->insert($this->_productCategoryTable,array(
					'product_id'=>$productId,
					'category_id'=>$category,
					'position'=>$position
			));
		}
		
		return $this;
	}
	
	function load(&$rowObj,$id=null, $fields = '*',$field = 'id'){
		$rowObj = parent::load(&$rowObj,$id, $fields,$field );
		if($rowObj->getId()){
			$rowObj->setData('images',$this->loadImages($rowObj))
			->setData('categories',$this->loadCategories($rowObj));
		}
	}
	
	function loadImages($rowObj){
		$select = $this->getDb()->select()->from($this->_imageTable)->where('product_id=?',$rowObj->getId())->order('position asc');
		return $this->getDb()->fetchAll($select);
	}
	
	function loadCategories($rowObj){
		$select = $this->getDb()->select()->from($this->_productCategoryTable)->where('product_id=?',$rowObj->getId())->order('position asc');
		return $this->getDb()->fetchAll($select);
	}
}