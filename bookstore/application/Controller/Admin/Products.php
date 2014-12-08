<?php
class Controller_Admin_Products extends Controller_Admin_Abstract {
	
	function indexAction() {
		$numberPerPage = 10;
		$totalCount = $this->getDbTableModel ( 'product' )->countTotal();
		
		$page = @$_GET['page']?$_GET['page']:1;
		if($page> $totalCount/2+1){
			$page = $totalCount/2+1;
		}
		
		$products = $this->getDbTableModel ( 'product' )->fetchAll (
			$this->getDbTableModel ( 'product' )->select()->limit($numberPerPage,($page-1)*$numberPerPage)
		);
		
		
		
		$this->view->products = $products;
		$this->view->numberPerPage = $numberPerPage;
		$this->view->totalCount = $totalCount;
	}
	
	function addAction() {
		
		if(App::isRequestPost()){
			//var_dump(App::getParam('categories'));die();
			try {
				$required_fieds = array(
				'name'=>'Product Name',
						'description'=>'Description',
						'short_description'=>'Short Description',
						'isbn'=>'ISBN',
						'author'=>"Author",
						'publish_year'=>'Publish Year',
						'price'=>"Price"
				);
				
				foreach ($required_fieds as $key=>$fieldName){
					if (!App::getParam($key)) {
						throw new Exception ( $fieldName.' is required.' );
					}
				}
				
				//throw new Exception ( 'test.' );
				
				
				
				$product = $this->getModel ( 'product' )->load ();
					
				foreach ($required_fieds as $key=>$fieldName){
					$product->setData ( $key,App::getParam($key));
				}
				
				$product->setData ( 'sale_price',App::getParam('sale_price'));
				$product->save();				
				
				//upload Images
				if(!empty($_FILES['images']['name'][0])){
					$this->_uploadProductImage($product,$_FILES['images']);
				}
				
				//save categories
				$product->setCategories(App::getParam('categories'));
				
				$this->flashMessage ( 'Product has been created successfully.' );
				
				$this->redirect ( BASEURL . '/admin.php?controller=products' );
			} catch ( Exception $e ) {
				$this->flashMessage ( $e->getMessage () ,'error');
				
				$this->view->postData = App::getParams();
			}
		}
		
		
		
		$this->view->categories = $this->getDbTableModel ( 'category' )->fetchAll (
				$this->getDbTableModel ( 'category' )->select()->order('category_name asc')
		);
		
		
	}
	
	private function _initProduct($id=null){
		if($id==null){
			$id = @ $_GET ['id'];
		}
		if (empty ($id )) {
			$this->error ( 'ID is not valid.' );
		}
		
	
		
		$product = $this->getModel ( 'product' )->load ( $id );
		
		if (!$product || ! $product->getId ()) {
			$this->error ( 'ID is not valid.' );
		}
		
		return $product;
	}

	
	function editAction(){
		$product = $this->_initProduct();
		
		if(App::isRequestPost()){
				
			try {
				$required_fieds = array('name'=>'Product Name',
						'description'=>'Description',
						'short_description'=>'Short Description',
						'isbn'=>'ISBN',
						'author'=>"Author",
						'publish_year'=>'Publish Year',
						'price'=>"Price");
		
				foreach ($required_fieds as $key=>$fieldName){
					if (!App::getParam($key)) {
						throw new Exception ( $fieldName.' is required.' );
					}
				}
		
		
					
				foreach ($required_fieds as $key=>$fieldName){
					$product->setData ( $key,App::getParam($key));
				}
		
				$product->setData ( 'sale_price',App::getParam('sale_price'));
				$product->save();
		
				//upload Images
				if(!empty($_FILES['images']['name'][0])){
					$this->_uploadProductImage($product,$_FILES['images']);
				}
				
				if(App::getParam('deleted_image')){
					$product->deleteImages(App::getParam('deleted_image'));
				}
		
				//save categories
				$product->setCategories(App::getParam('categories'));
		
				$this->flashMessage ( 'Product has been created successfully.' );
		
				$this->redirect ( BASEURL . '/admin.php?controller=products' );
			} catch ( Exception $e ) {
				$this->flashMessage ( $e->getMessage () ,'error');
			}
			
			$this->view->postData = App::getParams();
		}else{
			$this->view->postData = $product->toArray();
		}
		
	
		
		$this->view->categories = $this->getDbTableModel ( 'category' )->fetchAll (
				$this->getDbTableModel ( 'category' )->select()->order('category_name asc')
		);
		
	}
	
	/**
	 * delete product action
	 */
	function deleteAction(){
		try {
			$product = $this->_initProduct();
			$product->delete();
			$this->flashMessage ( 'Product has been deleted successfully.' );
		
		} catch ( Exception $e ) {
			$this->flashMessage ( $e->getMessage () );
		}
		
		$this->redirect ( BASEURL . '/admin.php?controller=products' );
		
	}
	private function _uploadProductImage($product,$files) {
		$i = 0;
		foreach ($files['name'] as $key=> $file) {
			$handle = new Upload ( $files ["tmp_name"][$key] );
			if ($handle->uploaded) {
				$handle->file_new_name_body = time().rand(10000,99999);
				$handle->file_new_name_ext = pathinfo($files ["name"][$key], PATHINFO_EXTENSION);
				$handle->process ( MEDIA_DIR . '/catalog/product' );
				if ($handle->processed) {
					
					//save to db
					$product->saveImage($handle->file_dst_name,$i,$i==0?true:false);
					$i++;
					
					$handle->clean ();
					
				} 
			}
		}
		
	}
}