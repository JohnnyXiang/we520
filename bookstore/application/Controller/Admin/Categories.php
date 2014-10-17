<?php
class Controller_Admin_Categories extends Controller_Admin_Abstract {
	
	function indexAction() {
		$numberPerPage = 2;
		$totalCount = $this->getDbTableModel ( 'category' )->countTotal();
		
		$page = @$_GET['page']?$_GET['page']:1;
		if($page> $totalCount/2+1){
			$page = $totalCount/2+1;
		}
		
		$categories = $this->getDbTableModel ( 'category' )->fetchAll (
			$this->getDbTableModel ( 'category' )->select()->limit($numberPerPage,($page-1)*$numberPerPage)
		);
		
		
		
		$this->view->categories = $categories;
		$this->view->numberPerPage = $numberPerPage;
		$this->view->totalCount = $totalCount;
	}
	function addAction() {
		
		if (! empty ( $_POST ['category_name'] )) {
			$category = $this->getModel ( 'category' )->load ();
			
			$image = '';
			if ($_FILES ['image'] ['name']) {
				$image = $this->_uploadCategoryImage ( $_FILES ['image'] );
			}
			
			try {
				
				if (empty ( $_POST ['category_name'] )) {
					throw new Exception ( 'Category name is required.' );
				}
				
				if (empty ( $_POST ['category_description'] )) {
					throw new Exception ( 'Category decription is required.' );
				}
				
				$category->setData ( 'category_name', $_POST ['category_name'] )
				->setData ( 'category_description', $_POST ['category_description'] )
				->setData ( 'position', $_POST ['position'] ? $_POST ['position'] : 0 )
				->setData ( 'category_image', $image )
				->setData ( 'status', $_POST ['status'] )
				->save ();
				
				$this->flashMessage ( 'Category has been created successfully.' );
				
				$this->redirect ( BASEURL . '/admin.php?controller=categories' );
			} catch ( Exception $e ) {
				$this->flashMessage ( $e->getMessage () );
			}
		}
	}
	
	private function _initCategory($id=null){
		if($id==null){
			$id = @ $_GET ['id'];
		}
		if (empty ($id )) {
			$this->error ( 'ID is not valid.' );
		}
		
	
		
		$category = $this->getModel ( 'category' )->load ( $id );
		
		if (! $category->getId ()) {
			$this->error ( 'ID is not valid.' );
		}
		
		return $category;
	}
	public function editAction() {
		
		$category = $this->_initCategory();
		
		
		if (! empty ( $_POST ['category_name'] )) {
			
			
			
			
			try {
				
				if (empty ( $_POST ['category_name'] )) {
					throw new Exception ( 'Category name is required.' );
				}
				
				if (empty ( $_POST ['category_description'] )) {
					throw new Exception ( 'Category decription is required.' );
				}
				
				$category->setData ( 'category_name', $_POST ['category_name'] )
				->setData ( 'category_description', $_POST ['category_description'] )
				->setData ( 'position', $_POST ['position'] ? $_POST ['position'] : 0 )			
				->setData ( 'status', $_POST ['status'] );
			
				if($_POST['delete_image']){
					
					unlink(MEDIA_DIR . '/catalog/category/'.$category->getCategoryImage());
					$category->setData('category_image','');
				}elseif($_FILES ['image'] ['name']){			
					$image = $this->_uploadCategoryImage ( $_FILES ['image'] );
					if($image){
						$category->setData('category_image',$image);
					}			
				}
				
				$category->save();
				
				$this->flashMessage ( 'Category has been updated successfully.' );
				
				$this->redirect ( BASEURL . '/admin.php?controller=categories' );
			} catch ( Exception $e ) {
				$this->flashMessage ( $e->getMessage () );
			}
		}
		
		
		$this->view->category = $category;
	
	}
	
	function deleteAction(){
		try {
			$category = $this->_initCategory();
			$category->delete();
			$this->flashMessage ( 'Category has been deleted successfully.' );
		
		} catch ( Exception $e ) {
			$this->flashMessage ( $e->getMessage () );
		}
		
		$this->redirect ( BASEURL . '/admin.php?controller=categories' );
		
	}
	private function _uploadCategoryImage($file) {
		$handle = new Upload ( $file );
		if ($handle->uploaded) {
			$handle->process ( MEDIA_DIR . '/catalog/category' );
			if ($handle->processed) {
				$handle->clean ();
				return $handle->file_dst_name;
			} else {
				return '';
			}
		}
	}
}