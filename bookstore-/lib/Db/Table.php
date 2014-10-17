<?php
Class Db_Table{
  	protected $_db;
  	protected $_tableName;
  	
	public function __construct(){
		$db = Registry::get('db');
		$this->_db = $db;
	}

	
    
    public function query($query){
    	return $this->_db->query($query);
    }
    
	public function select($join, $columns = null, $where = null) {		
		return $this->_db->select($this->_tableName,$join, $columns, $where);
	}
	
    public function insert($datas) {
    	return $this->_db->insert($this->_tableName, $datas);
    }
    
    public function update($data, $where=null){
    	return $this->_db->update($this->_tableName, $data,$where);
    }
    
	public function delete($where) {
		return $this->_db->delete($this->_tableName,$where);
	}
	
	function getById($id){
		return $this->select('*',array('id[=]'=>$id));
	}
}