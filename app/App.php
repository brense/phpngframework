<?php

require_once('../' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'BasicApplication.php');

new App(
	array(
		'env' => array(
			'localhost:8080' => array(
				'debug' => true,
				'db' => array(
					'type' => 'json',
					'data' => 'data' . DIRECTORY_SEPARATOR
				)
			),
			'band-base.com' => array(
				'debug' => false
			)
		),
		'website.title' => 'My App',
		'templates.path' => 'public' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR,
		'encryption.key' => 'ISYvCYyZ5HgMuN285aHv',
		'encryption.cipher' => 'rijndael-192',
		'sources' => array(
			'app' . DIRECTORY_SEPARATOR,
			'app' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR,
			'framework' . DIRECTORY_SEPARATOR,
			'framework' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR,
			'framework' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
		),
		'controllers' => array(
			'ContentController',
			'MainController'
		),
		'routes' => array(
			array(
				'requestMethod' => 'GET',
				'route' => 'test',
				'callback' => function(){
					echo 'test';exit;
					/*
					$cursor = Mapper::instance()->test->find();
					foreach($cursor as $document)
						print_r($document);
					*/
				}
			)
		)
	)
);