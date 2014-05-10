<?php

namespace database;

use \Iterator;

class Collection implements Iterator {
	
	private $_position;
	private $_items;

	public function __construct(Array $list = array()){
		$this->_position = -1;
		foreach($list as $item)
			$this->_items[] = $item;
	}

	public function rewind(){
		$this->_position = -1;
	}

	public function current(){
		$pos = $this->_position;
		if($pos < 0) $pos = 0;
		if(isset($this->_items[$pos]))
			return $this->_items[$pos];
		else
			return false;
	}

	public function key(){
		return $this->_position;
	}

	public function next(){
		$this->_position++;
		if(isset($this->_items[$this->_position]))
			return $this->_items[$this->_position];
		else
			return false;
	}

	public function previous(){
		$this->_position--;
		if(isset($this->_items[$this->_position]))
			return $this->_items[$this->_position];
		else
			return false;
	}

	public function valid($reverse = false){
		if(!$reverse && isset($this->_items[$this->_position + 1]))
			return true;
		if($reverse && isset($this->_items[$this->_position - 1]))
			return true;
		return false;
	}

	public function append($item){
		$this->_items[] = $item;
	}

	public function remove($pos){
		$arr = array();
		foreach($this->_items as $k => $item){
			if($k != $pos)
				$arr[] = $item;
		}
		$this->_items = $arr;
	}

	public function count(){
		return count($this->_items);
	}

	public function seek($pos){
		$this->_position = $pos;
	}

	// TODO: sorting methods?

}