<?php

namespace database\models;

abstract class Saveable {
	
	protected $_id = -1;

	public static function find(Array $criteria = array()){
		$caller = get_called_class();
		return self::getMapper()->read($criteria, new $caller());
	}

	public static function findOne(Array $criteria = array()){
		$results = self::find($criteria);
		if(isset($results[0]))
			return $results[0];
		else
			return false;
	}

	public function save(Array $values = array()){
		if(!isset($values))
			$values = $this->getProperties();
		if($this->_id >= 0)
			return self::getMapper()->update($this, $values);
		else
			return self::getMapper()->create($this);
	}

	public function delete(){
		return self::getMapper()->delete($this);
	}

	public static function __callStatic($method, Array $parameters = array()){
		if(substr($method, 0, 8) == 'find_by_'){
			$criteria = array(substr($method, 8) => $parameters[0]);
			return self::find($criteria);
		}
	}

	public function getProperties(){
        $arr = array();
		foreach($this as $k => $v){
            $arr[substr($k, 1)] = $v;
		}
		return $arr;
    }
    
    public function populate(){
        return true;
    }
    
    public function __get($property){
		if(substr($property, 0, 1) != '_')
			$property = '_' . $property;
		if(property_exists($this, $property))
			return $this->$property;
	}
	
	public function __set($property, $value){
		if(substr($property, 0, 1) != '_')
			$property = '_' . $property;
		if(property_exists($this, $property))
			$this->$property = $value;
	}
	
    public function __isset($property){
		if(substr($property, 0, 1) != '_')
			$property = '_' . $property;
		if(property_exists($this, $property))
			return true;
		return false;
	}

	private static function getMapper(){
		switch(\App::config()->db['type']){
			case 'json':
				return new \database\JsonMapper(
					new \database\JsonDatabase(\App::config()->document_root . \App::config()->db['data'])
				);
				break;
			case 'mongo':
				return new \database\MongoMapper(
					new \database\MongoDatabase(\App::config()->db['user'], \App::config()->db['pswd'], \App::config()->db['host'], \App::config()->db['name'])
				);
				break;
		}
	}
}