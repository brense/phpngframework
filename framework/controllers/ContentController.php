<?php

namespace controllers;

use \App;
use \Mapper;
use \Config;
use \models\Content;
use \models\Language;

class ContentController extends \routing\RequestController {
	
	public $route_getContent = array('GET', 'content');
	public function getContent(){
		// TODO: check login!
		$content = Content::findOne();
		echo json_encode($content->getProperties());
	}
	
	public $route_updateContent = array('PUT', 'content/:id');
	public function updateContent($id){
		// TODO: check login!
		$content = Content::findOne(array('id' => $id));
		$content->save((array)App::request()->parameters);
	}
	
	public $route_removeContentLanguage = array('DELETE', 'content/language');
	public function removeContentLanguage(){
		// TODO: check login!
		$content = Content::findOne();
		$values = $this->recursiveRemoveLanguage($content->getProperties(), App::request()->parameters->lang);
		$content->save($values);
		echo json_encode($content->getProperties());
	}
	
	public $route_getLanguages = array('GET', 'languages');
	public function getLanguages(){
		// TODO: check login!
		$languages = Language::find();
		$arr = array();
		foreach($languages as $language){
			$arr[] = $language->getProperties();
		}
		echo json_encode($arr);
	}
	
	private function recursiveRemoveLanguage($obj, $lang){
		$new = new \StdClass();
		foreach($obj as $k => $v) {
			if(isset($v->$lang)){
				unset($v->$lang);
				$new->$k = $v;
			} else if(is_object($v) || is_array($v)){
				$new->$k = $this->recursiveRemoveLanguage($v, $lang);
			} else {
				$new->$k = $v;
			}
		}
		return $obj;
	}
	
}