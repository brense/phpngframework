<?php

namespace database;

use \database\interfaces\iMapper;
use \database\interfaces\iDatabase;
use \database\models\Saveable;

class JsonMapper implements iMapper {
	
	private $_conn;

	public function __construct(iDatabase $conn){
		$this->_conn = $conn;
	}

	public function create(Saveable $saveable){
		// TODO: map before returning
	}

	public function read(Array $criteria, Saveable $obj){
		$results = $this->_conn->read(get_class($obj), $criteria);
		$arr = array();
		while($item = $results->next()){
			$o = clone $obj;
			$i = $this->map($item, $o);
			$i->id = $results->key();
			$arr[] = $i;
		}
		return $arr;
	}

	public function update(Saveable $obj, Array $values){
		$obj = $this->map($values, $obj);
		$this->_conn->update(get_class($obj), array('id' => $obj->id), $obj->getProperties());
	}

	public function delete(Saveable $saveable){
		// TODO
	}

	private function map($item, Saveable $obj){
		foreach($item as $k => $v){
			$obj->$k = $v;
		}
		$obj->populate();
		return $obj;
	}

}