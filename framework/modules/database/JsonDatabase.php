<?php

namespace database;

use \database\interfaces\iDatabase;

class JsonDatabase implements iDatabase {

	private $_location;

	public function __construct($location){
		$this->_location = $location;
	}

	public function create(){

	}

	public function read($type, Array $query = array(), Array $fields = array()){
		$json = json_decode(file_get_contents($this->_location . str_replace('\\', DIRECTORY_SEPARATOR, strtolower($type)) . '.json'));
		
		$collection = new Collection();
		foreach($json as $id => $obj){
			$match = true;
			if(!(isset($query['id']) && $query['id'] == $id)){
				foreach($query as $k => $v){
					if($obj->$k != $v)
						$match = false;
				}
			}
			if($match){
				if(count($fields) > 0){
					$newObj = new \StdClass();
					foreach($fields as $f){
						$newObj->$f = $obj->$f;
					}
				} else {
					$newObj = $obj;
				}
				$collection->append($newObj);
			}
		}
		// TODO: collection sorting?
		return $collection;
	}

	public function update($type, Array $query = array(), Array $values = array()){
		$json = json_decode(file_get_contents($this->_location . str_replace('\\', DIRECTORY_SEPARATOR, strtolower($type)) . '.json'));

		foreach($json as $id => &$obj){
			$match = true;
			if(!(isset($query['id']) && $query['id'] == $id)){
				foreach($query as $k => $v){
					if($k != 'id' && $obj->$k != $v)
						$match = false;
				}
			}
			if($match){
				foreach($values as $k => $v){
					if($k != 'id')
						$obj->$k = $v;
				}
			}
		}

		file_put_contents($this->_location . str_replace('\\', DIRECTORY_SEPARATOR, strtolower($type)) . '.json', json_encode($json));
	}

	public function delete(){

	}

	public function query(){

	}
	
}