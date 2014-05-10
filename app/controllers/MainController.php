<?php

namespace controllers;

use \App;
use \models\User;
use \models\Page;

class MainController extends \routing\RequestController {
	
	public $route_main = array('GET', ':page');
	public function main($page = 'home'){
		$page = new Page($page);
		$page->show();
	}
	
}