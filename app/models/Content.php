<?php

namespace models;

use \database\models\Saveable;

class Content extends Saveable {
	
	protected $_title;
	protected $_sections = array();
	protected $_footer;

}