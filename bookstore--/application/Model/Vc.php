<?php
abstract class Model_Vc extends Zend_Db_Table_Abstract {

	private $start;
	private $end;
	public $db;
	private $exectime;


	const STATUS_DELETED_MERGED = 4;
	const STATUS_DELETED_SELF = 3;
	const STATUS_DELETED_FLAG = 2;

	public function __call($name, $value) {
		if (strpos ( $name, 'getAllBy' ) !== false) {
			$fieldName = str_replace ( 'getAllBy', '', $name );
			$field = Inflector::underscore ( $fieldName );
				
			return $this->fetchAll ( $this->select ()->where ( "$field = ?", $value )->order ( "id Desc" ) );

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
		$this->starttimer ();
		$result = $this->db->query ( $sql );
		$this->endtimer ();
		return $result;
	}

	function insert($data) {
		$cols = $this->info ( Zend_Db_Table_Abstract::COLS );

		foreach ( $data as $key => $value ) {
			if (! in_array ( $key, $cols )) {
				unset ( $data [$key] );
			}
		}

		//$data = $this->escapeData ( $data );


		if (isset ( $data ['id'] ) && ! empty ( $data ['id'] ) && $data ['id'] != '') {
				
			$t = $this->getById ( $data ['id'] );
			if (isset ( $t->id )) {
				if (in_array ( 'time_updated', $cols )) {
					$data ['time_updated'] = Timer::DatetimeInGMT ();
						
				}

				if (in_array ( 'date', $cols ) && ! isset ( $data ['date'] )) {
					$data ['date'] = Timer::DatetimeInGMT ();
					;

				}

				unset ( $data ['time_created'] );
				unset ( $cols );
				$res = $this->update ( $data, $this->getAdapter ()->quoteInto ( 'id=?', $data ['id'] ) );
			} else {
				if (in_array ( 'time_created', $cols ) && ! isset ( $data ['time_created'] )) {
					$data ['time_created'] = Timer::DatetimeInGMT ();
					;

				}
				if (in_array ( 'date', $cols ) && ! isset ( $data ['date'] )) {
					$data ['date'] = Timer::DatetimeInGMT ();
					;

				}

				unset ( $cols );
				$res = parent::insert ( $data );
			}

		} else {
			unset ( $data ['id'] );
				
			if (in_array ( 'time_created', $cols ) && ! isset ( $data ['time_created'] )) {
				$data ['time_created'] = Timer::DatetimeInGMT ();			
			}
				
			if (in_array ( 'date', $cols ) && ! isset ( $data ['date'] )) {
				$data ['date'] = Timer::DatetimeInGMT ();
				;
					
			}
				
			unset ( $cols );
			//var_dump($data);die();
			$res = parent::insert ( $data );

		}

		unset ( $data );

		return $res;
	}
	function update($data, $where) {
		$cols = $this->info ( Zend_Db_Table_Abstract::COLS );
		foreach ( $data as $key => $value ) {
			if (! in_array ( $key, $cols )) {
				unset ( $data [$key] );
			}
		}

		if (in_array ( 'time_updated', $cols )) {
			$data ['time_updated'] = Timer::DatetimeInGMT ();
			;
			;
		}

		if (in_array ( 'date', $cols ) && ! isset ( $data ['date'] )) {
			$data ['date'] = Timer::DatetimeInGMT ();

		}

		//$data = $this->escapeData ( $data );


		return parent::update ( $data, $where );
	}

	function updateById($id, $data) {

		return $this->update ( $data, 'id="' . $id . '"' );
	}
	function getById($id, $field = 'id') {
		return $this->fetchRow ( $this->select ()->where ( "$field = ?", $id ) );

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
		$exectime = @$endtime [0] + @$endtime [1] - @$starttime [0] - @$starttime [1];
		$this->exectime += $exectime;
		return $exectime;
	}

	function lastInsertId() {
		return $this->getDb ()->lastInsertId ();
	}

	function escapeData($data) {
		return $data;
	}

	function getAll($where = null, $start = 0, $limit = 30, $order = null) {
		$select = $this->select ()->limit ( $limit, $start );
		$cols = $this->info ( Zend_Db_Table_Abstract::COLS );

		if (is_array ( $where )) {
			if (isset ( $where ['cols'] )) {

				foreach ( $where ['cols'] as $key => $value ) {
					//var_dump ( $where ['value'] [$key] );
					if (! in_array ( $value, $cols )) {
						continue;
					}
						
					if ((! isset ( $where ['value'] [$key] ) || empty ( $where ['value'] [$key] )) && $where ['value'] [$key] !== 0) {
						continue;
					}
						
					if (strpos ( $where ['value'] [$key], '~' )) {
						list ( $from, $to ) = explode ( '~', $where ['value'] [$key] );
						$from = trim ( $from );
						$to = trim ( $to );

						if ($from > $to && $to > 0) {
							$a = $from;
							$from = $to;
							$to = $a;
						}

						if ($from > 0) {
							$select->where ( "$value>=?", $from );
						}

						if ($to > 0) {
							$select->where ( "$value<=?", $to );
						}
							
					} else {
						$values = explode ( ',', trim ( $where ['value'] [$key] ) );
						$select->where ( "$value in (?) ", $values );
					}

				}

				unset ( $where ['col'], $where ['value'] );
			}
				
			foreach ( $where as $key => $value ) {

				if (! in_array ( $key, $cols )) {
					continue;
				}

				if (is_array ( $value )) {
					if (isset ( $value ['from'] ) || isset ( $value ['to'] )) {
						$yearfrom = @$value ['from'];
						$yearto = @$value ['to'];
						if ($yearfrom > $yearto && $yearto > 0) {
							$a = $yearfrom;
							$yearfrom = $yearto;
							$yearto = $a;
						}

						if ($yearfrom > 0) {
							$select->where ( "$key>=?", $yearfrom );
						}

						if ($yearto > 0) {
							$select->where ( "$key<=?", $yearto );
						}
							
					} elseif (isset ( $value ['notequal'] )) {
						$select->where ( "$key!=?", $value ['notequal'] );
					} else {
						$value = array_filter ( $value );
						if (empty ( $value ) && $value !== 0)
							continue;
						$select->where ( "$key in (?) ", $value );
					}
				} else {
						
					if (empty ( $value ) && $value !== 0) {
						continue;
					}
						
					$select->where ( "$key=?", $value );
				}
					
			}
		}

		if ($order != null) {
			$select->order ( $order );
		}
		//echo $select;
		return $this->fetchAll ( $select );
	}

	function buildWhere($where) {
		$select = $this->select ();
		$cols = $this->info ( Zend_Db_Table_Abstract::COLS );

		if (is_array ( $where )) {
			if (isset ( $where ['cols'] )) {

				foreach ( $where ['cols'] as $key => $value ) {
					//var_dump ( $where ['value'] [$key] );
					if (! in_array ( $value, $cols )) {
						continue;
					}
						
					if (! isset ( $where ['value'] [$key] ) || empty ( $where ['value'] [$key] )) {
						continue;
					}
						
					if (strpos ( $where ['value'] [$key], '~' )) {
						list ( $from, $to ) = explode ( '~', $where ['value'] [$key] );
						$from = trim ( $from );
						$to = trim ( $to );

						if ($from > $to && $to > 0) {
							$a = $from;
							$from = $to;
							$to = $a;
						}

						if ($from > 0) {
							$select->where ( "$value>=?", $from );
						}

						if ($to > 0) {
							$select->where ( "$value<=?", $to );
						}
							
					} else {
						$values = explode ( ',', trim ( $where ['value'] [$key] ) );
						$select->where ( "$value in (?) ", $values );
					}

				}

				unset ( $where ['col'], $where ['value'] );
			}
				
			foreach ( $where as $key => $value ) {

				if (! in_array ( $key, $cols )) {
					continue;
				}

				if (is_array ( $value )) {
					if (isset ( $value ['from'] ) || isset ( $value ['to'] )) {
						$yearfrom = @$value ['from'];
						$yearto = @$value ['to'];
						if ($yearfrom > $yearto && $yearto > 0) {
							$a = $yearfrom;
							$yearfrom = $yearto;
							$yearto = $a;
						}

						if ($yearfrom > 0) {
							$select->where ( "$key>=?", $yearfrom );
						}

						if ($yearto > 0) {
							$select->where ( "$key<=?", $yearto );
						}
							
					} else {
						$value = array_filter ( $value );
						if (empty ( $value ))
							continue;
						$select->where ( "$key in (?) ", $value );
					}
				} else {
						
					if (empty ( $value ))
						continue;
						
					$select->where ( "$key=?", $value );
				}
					
			}
		}

		return $select;

	}
	function count($where = null) {
		$cols = $this->info ( Zend_Db_Table_Abstract::COLS );

		$select = $this->buildWhere ( $where );
		$select->from ( $this, array ('count(*) as amount' ) );
		$rows = $this->fetchAll ( $select );

		return ($rows [0]->amount);

		$sql = "SELECT COUNT(*) AS count from " . $this->_name . " WHERE 1 ";
		if (is_array ( $where )) {
				
			foreach ( $where as $key => $value ) {
				if (! in_array ( $key, $cols )) {
					continue;
				}

				$sql .= " AND $key = '{$value}'";
			}
		} elseif ($where != null) {
			$sql .= $where;
		}

		$data = $this->_db->fetchRow ( $sql );

		return $data->count;
	}

	



	

	
}
