<?php

namespace database;

class MongoDatabase {
	
	private $_conn;

	public function __construct($user, $pswd, $host, $dbname){
		$auth = $user . ':' . $pswd . '@';
		$this->_conn = new MongoClient('mongodb://' . $auth . $host . ':27017/' . $dbname);
	}
}