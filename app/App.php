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
			)
		)
	)
);