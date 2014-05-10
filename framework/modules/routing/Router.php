<?php

namespace routing;

class Router {

	private $_routes = array();

	public function __construct(){

	}

	public function resolveRoute(){
		$this->sortRoutes();
		
		$route = $this->matchRoute();

		if(!$route){
			echo '404'; // TODO: 404
		} else {
			call_user_func_array($route['callback'], $route['parameters']);
		}
	}

	public function registerControllers($controllers){
		if($controllers != null){
			foreach($controllers as $class){
				$class = '\controllers\\' . $class;
				$controller = new $class();
				if($controller instanceof \controllers\RequestController){
					foreach($controller->getRequestMappings() as $route){
						$this->registerRoute($route[0], $route[1], $route[2]);
					}
				}
			}
		}
	}

	public function registerRoute($requestMethod, $route, $callback){
		$this->_routes[$requestMethod][$route] = $callback;
	}

	private function sortRoutes(){
		$routeCount = count(\App::request()->uri);
		uksort($this->_routes[$_SERVER['REQUEST_METHOD']], function($a, $b) use ($routeCount) {
			$arra = explode('/', $a);
			$acount = count($arra);
			if(substr($arra[$acount-1], 0, 1) == ':')
				$acount -= 0.5;
			$arrb = explode('/', $b);
			$bcount = count($arrb);
			if(substr($arrb[$bcount-1], 0, 1) == ':')
				$bcount -= 0.5;
			if($routeCount == $acount)
				$acount += 1;
			if($routeCount == $bcount)
				$bcount += 1;
			if($bcount > $acount)
				return 1;
			elseif($bcount < $acount)
				return -1;
			else
				return 0;
		});
	}

	private function matchRoute(){
		$selected = array();
		foreach($this->_routes[\App::request()->method] as $route => $callback){
			$arr = explode('/', $route);
			$n = 0;
			foreach($arr as $value){
				// determine if the route part matches the requested url part
				if(substr($value, 0, 1) == ':' || (isset(\App::request()->uri[$n]) && \App::request()->uri[$n] == $value)){
					$selected['callback'] = $callback;
					$selected['parameters'] = array();
					if(substr($value, 0, 1) == ':'){
						if(isset(\App::request()->uri[$n])){
							$selected['parameters'][substr($value, 1)] = \App::request()->uri[$n];
						} else {
							$selected['parameters'][substr($value, 1)] = implode(\App::request()->uri);
						}
					}
				} else {
					$selected = array();
					break;
				}
				// handle remaining parts of the requested uri
				if(!isset($arr[$n+1]) && isset($selected['callback']) && isset(\App::request()->uri[$n+1])){
					if(isset($selected['parameters'][substr($value, 1)])){
						for($i = $n+1; $i < count($uri); $i++){
							$selected['parameters'][substr($value, 1)] .= '/' . \App::request()->uri[$i];
						}
					}
					return $selected;
				}
				$n++;
			}
			// break the loop if a suitable callback has been found
			if(isset($selected['callback'])){
				return $selected;
			}
		}
		return false;
	}

}