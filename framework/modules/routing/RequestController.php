<?php

namespace routing;

abstract class RequestController {
	public function getRequestMappings(){
		$mappings = array();
		$vars = get_class_vars(get_class($this));
		foreach($vars as $name => $route){
			if(substr($name, 0, 6) == 'route_'){
				$func = substr($name, 6);
				$mappings[] = array($route[0], $route[1], array($this, $func));
			}
		}
		return $mappings;
	}
}