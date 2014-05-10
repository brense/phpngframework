<?php

namespace routing;

class Request {

	private static $_instance;
	private $_uri = array();
	private $_parameters = array();
	private $_method;
	
	private function __construct(){
		$requestUri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
		$requestUri = str_replace(strtolower(\App::config()->base_uri), '', 'http://' . $_SERVER['HTTP_HOST'] . $requestUri);
		$this->_uri = explode('/', $requestUri);
		for($i = 0; $i < count($this->_uri); $i++){
			if(strlen($this->_uri[$i]) == 0){
				unset($this->_uri[$i]);
			}
		}

		$this->_method = $_SERVER['REQUEST_METHOD'];

		$this->extractRequestParameters();
	}

	public static function instance(){
		if(empty(self::$_instance))
			self::$_instance = new self();
		return self::$_instance;
	}

	public function __get($property){
		if(property_exists($this, '_' . $property))
			return $this->{'_' . $property};
		return false;
	}

	public function __set($property, $value){
		if(property_exists($this, '_' . $property))
			$this->{'_' . $property} = $value;
	}

	public function __isset($property){
		if(property_exists($this, '_' . $property))
			return true;
		return false;
	}

	private function extractRequestParameters(){
		$putData = file_get_contents('php://input');
		
		switch($this->_method){
			case 'GET':
				if(isset($_GET) && count($_GET) > 0){
					$this->_parameters = (object)$_GET;
				}
				break;
			case 'POST':
				if(isset($_POST) && count($_POST) > 0){
					$this->_parameters = (object)$_POST;
				} else if(strlen($putData) > 0){
					$this->_parameters = json_decode($putData);
				}
				break;
			case 'PUT':
				if(strlen($putData) > 0){
					$this->_parameters = json_decode($putData);
				}
				break;
			case 'DELETE':
				if(isset($_GET) && count($_GET) > 0){
					$this->_parameters = (object)$_GET;
				} else if(strlen($putData) > 0){
					$this->_parameters = json_decode($putData);
				}
				break;
		}
	}

}