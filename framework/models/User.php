<?php

namespace models;

use \App;
use \database\models\Saveable;

class User extends Saveable {

	protected $_name;
	protected $_email;
	protected $_password;
	protected $_admin = false;

	public static function getLogin(){
		$user = null;
		if(App::session() && App::session()->login){
			$user = App::session()->login;
		}
		return $user;
	}

	public static function login($email, $password){
		$encryptedPassword = App::encrypt($password);
		$user = self::findOne(array('email' => $email, 'password' => $encryptedPassword));
		App::session()->login = $user;
	}

	public function checkLogin(){
		$check = App::session()->login;
		$user = self::findOne(array('email' => $check->email, 'password' => $check->password));
		if($user instanceof User){
			App::session()->login = $user;
			return true;
		}
		return false;
	}

	public function isAdmin(){
		return (bool)$this->_admin;
	}
	
}