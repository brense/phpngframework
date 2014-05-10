<?php

namespace controllers;

use \App;
use \models\User;
use \models\Page;

class AdminController extends \routing\RequestController {
	
	public $route_admin = array('GET', 'admin/:page');
	public function admin($page = ''){
		$user = User::getLogin();
		if($user instanceof User && $user->checkLogin() && $user->isAdmin()){
			$page = new Page('admin');
			$page->show();
		} else {
			$this->adminLoginPage();
		}
	}

	public $route_adminLoginPage = array('GET', 'admin/login');
	public function adminLoginPage(){
		$page = new Page('admin_login');
		$page->show();
	}

	public $route_adminLogin = array('POST', 'admin/login');
	public function adminLogin(){
		$params = App::request()->parameters;
		if(isset($params->email, $params->password))
			$user = User::login($params->email, $params->password);
		else
			throw new \Exception('login parameters are missing');
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}

	public $route_adminLogout = array('GET', 'admin/logout');
	public function adminLogout(){
		App::session()->login = null;
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}
}