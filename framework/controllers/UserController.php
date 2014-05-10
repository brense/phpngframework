<?php

namespace controllers;

use \App;
use \models\User;

class UserController extends \routing\RequestController {
	
	public $route_getUsers = array('GET', 'api/user');
	public function getUsers(){
        // TODO: check login!
		$users = User::find();
		$arr = array();
		foreach($users as $user){
			$arr[] = $user->getProperties();
		}
		echo json_encode($arr);
	}

    public $route_getUser = array('GET', 'api/user/:id');
	public function getUser($id){
		// TODO: check login!
		$user = User::findOne(array('id' => $id));
		echo json_encode($user->getProperties());
	}
	
}