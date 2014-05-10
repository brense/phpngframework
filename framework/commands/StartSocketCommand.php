<?php

namespace commands;

use \interfaces\iCommand;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Socket;

class StartSocketCommand implements iCommand {
	
	public function execute(Array $parameters = array()){
		$port = 8282;
		if(isset($parameters[0]))
			$port = $parameters[0];
		$server = IoServer::factory(
			new HttpServer(
				new WsServer(
					new Socket()
				)
			),
			$port
		);
		$server->run();
	}
	
}