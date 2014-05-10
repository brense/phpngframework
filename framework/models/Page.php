<?php

namespace models;

use \App;

class Page {

	private $_contents;
	
	public function __construct($page = 'home'){
		$this->_contents = file_get_contents(App::config()->document_root . App::config()->templates_path . $page . '.html');
		$this->_contents = str_replace('<head>', '<head>' . "\n    " . '<base href="' . App::config()->base_uri . '">', $this->_contents);
        if(isset(App::session()->login) && App::session()->login instanceof User)
		    $this->_contents = str_replace('</head>', '<script>var user = ' . json_encode(App::session()->login->getProperties()) . ';</script></head>', $this->_contents);
		$this->_contents = str_replace('<title>', '<title>' . App::config()->website_title . ' ', $this->_contents);
	}

	public function show(){
		echo $this->_contents;
	}
}