<?php
Abstract class Model_DbTable_Abstract extends Zend_Db_Table_Abstract{
	private $start;
	private $end;
	protected $db;
	private $exectime;
	private $_cache;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct ();
		//$this->_cache = Zend_Registry::get ( "db_cache" );
	}
	
	
	public function __call($name, $value) {
		if (strpos ( $name, 'getAllBy' ) !== false) {
			$fieldName = str_replace ( 'getAllBy', '', $name );
			$field = Inflector::underscore ( $fieldName );

			return $this->fetchAll ( $this->select ()->where ( "$field = ?", $value ) );

		} else if (strpos ( $name, 'getBy' ) !== false) {
			$fieldName = str_replace ( 'getBy', '', $name );
			$field = Inflector::underscore ( $fieldName );

			return $this->fetchRow ( $this->select ()->where ( "$field = ?", $value ) );

		}
	}

	public function getDb() {
		return Zend_Registry::get ( 'db' );
	}

	public function query($sql) {
		//$this->starttimer ();
		$result = $this->db->query ( $sql );
		//$this->endtimer ();
		return $result;
	}

	function cachedQuery($select, $type = "fetchRow", $db_model = null,$cache_key=null) {

		if ($db_model == null) {
			$db_model = $this->getAdapter ();
		}

		if (! is_object ( $db_model )) {
			return false;
		}

		return $result = $db_model->{$type} ( $select );;
		
		$cache = Zend_Registry::get ( "db_cache" );
		$uniqueCacheId = serialize ( is_object ( $select ) ? $select->__toString () : $select );
		$uniqueCacheId = md5 ( $uniqueCacheId );

		$result = $cache->load ( $uniqueCacheId );

		if ($result === false) {
			$result = $db_model->{$type} ( $select );


			if ($result === false) {
				$result = '';
			}
			$keys = array($this->_name);
			if($cache_key){
				$keys[] = $cache_key;
			}
			$cache->save ( $result, $uniqueCacheId,$keys );

		} else {
			if ($result == '') {
				$result = null;
			}
		}

		return $result;
	}

	function insert($data) {

		

		$cols = $this->info ( Model_DbTable_Abstract::COLS );

		foreach ( $data as $key => $value ) {
			if (! in_array ( $key, $cols )) {
				unset ( $data [$key] );
			}
		}

		$data = $this->escapeData ( $data );

		if (isset ( $data ['id'] ) && ! empty ( $data ['id'] ) && $data ['id'] != '') {

			$t = $this->getById ( $data ['id'] );
			if (isset ( $t->id )) {
				if (in_array ( 'updated', $cols )) {
					$data ['updated'] = time ();
					;
				}
				unset ( $data ['created'] );

				return $this->update ( $data, $this->getAdapter ()->quoteInto ( 'id=?', $data ['id'] ) );
			} else {
				if (in_array ( 'created', $cols ) && ! isset ( $data ['created'] )) {
					$data ['created'] = time ();

				}

				return parent::insert ( $data );
			}

		} else {
			unset ( $data ['id'] );
			if (in_array ( 'created', $cols ) && ! isset ( $data ['created'] )) {
				$data ['created'] = time ();
				;
			}
			return parent::insert ( $data );
		}
	}
	
	function update($data, $where) {
		
		$cols = $this->info ( Model_DbTable_Abstract::COLS );

		//var_dump($cols,$data);


		foreach ( $data as $key => $value ) {
			if (! in_array ( $key, $cols )) {
				unset ( $data [$key] );
			}
		}

		if (in_array ( 'updated', $cols )) {
			$data ['updated'] = time ();
			;
		}
		$data = $this->escapeData ( $data );

		return parent::update ( $data, $where );
	}

	function delete($where){
		

		return parent::delete($where);
	}

	function updateById($id, $data) {

		return $this->update ( $data, 'id="' . $id . '"' );
	}
	
	function getById($id, $field = 'id', $fields = '*') {
		if(empty($field)){
			$field = 'id';
		}
		$select =  $this->select ()->where ( "$field = ?", $id );
		return $this->fetchRow ($select  );
	}
	
	function load(&$rowObj,$id=null, $fields = '*',$field = 'id'){
		if($id==null){
			$rowObj = $this->createRow();
		}else{
			$rowObj = $this->getById($id, $field, $fields);
		}
		
		return $rowObj;
	}

	function deleteById($id) {
		return $this->delete ( 'id="' . $id . '"' );
	}

	function getLargestId() {
		$data = $this->fetchRow ( $this->select ()->order ( 'id Desc' )->limit ( 1 ) );
		return $data->id;
	}

	function starttimer() {
		$this->start = microtime ();
	}

	function endtimer() {
		$this->end = microtime ();
	}

	function getExectime() {
		$starttime = explode ( " ", $this->start );
		$endtime = explode ( " ", $this->end );
		$exectime = $endtime [0] + $endtime [1] - $starttime [0] - $starttime [1];
		$this->exectime += $exectime;
		return $exectime;
	}

	function lastInsertId() {
		return $this->getDb ()->lastInsertId ();
	}

	function escapeData($data) {
		//		foreach($data as $key=>$value){
		//				$data[$key] = htmlentities ($value);
		//		}
		//		echo "Magic quotes is " . (get_magic_quotes_gpc() ? "ON" : "OFF");
		//


		return $data;
	}
	
	function countTotal(){
		$sql = "select count(*) as total from ". $this->_name;
		$row = $this->getDb()->fetchRow($sql);
		
		return $row->total;
	}
}
