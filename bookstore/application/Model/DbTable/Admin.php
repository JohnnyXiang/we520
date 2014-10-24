<?php
/**
 * admin Model.
 */
class Model_DbTable_Admin extends Model_DbTable_Abstract {
	protected $_rowClass = 'Model_Admin';
	protected $_name = 'admins';
	
	function verifyLogin($username,$password){
		$select= $this->select()->where('username=?',$username);
		$data = $this->fetchRow($select);
		if(empty($data)){
			return false;
		}else{			
			$bcrypt = new Bcrypt();									
			if ($bcrypt->verify($password, $data->password)) {
				return $data;
			}else{
				return false;
			}
		}
	}
}

