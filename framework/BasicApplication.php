<?php

class App {

	private static $_instance;
	private $_config;
	private $_routes = array();
	private $_request;
	private $_encryptionKey;
	private $_cipher;
	private $_session;

	public function __construct(Array $config = array()){
		// create static instance of self
		self::$_instance = &$this;

		// save config
		$this->_config = new Config($config);
		$this->_encryptionKey = $config['encryption.key'];
		$this->_cipher = $config['encryption.cipher'];

		// display php erros when debugging
		if($this->_config->debug){
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}

		// set document root
		$this->_config->document_root = str_replace('framework' . DIRECTORY_SEPARATOR . 'BasicApplication.php', '', __FILE__);

		// set base uri
		$remaining = str_replace(str_replace('/', DIRECTORY_SEPARATOR , $_SERVER['DOCUMENT_ROOT']), '', $this->_config->document_root);
		$this->_config->base_uri = 'http://' . $_SERVER['HTTP_HOST'] . str_replace(DIRECTORY_SEPARATOR, '/', $remaining);

		// register autoloader
		if(!isset($this->_config->autoloader))
			spl_autoload_register(array($this, 'autoload'));
		else
			spl_autoload_register($this->_config->autoloader);

		// set the exception handler
		if(isset($this->_config->exceptionHandler)){
			set_exception_handler($this->_config->exceptionHandler);
		} else {
			set_exception_handler(function(Exception $e){
				if($this->_config->debug){
					echo '<pre>';print_r($e);echo '</pre>';
				}
			});
		}
		
		// set the error handler
		if(isset($this->_config->errorHandler)){
			set_error_handler($this->_config->errorHandler);
		} else {
			set_error_handler(function($errno, $errstr, $error_file = null, $error_line = null, Array $error_context = null){
				$error = array('no' => $errno, 'error' => $errstr, 'file' => $error_file, 'line' => $error_line, 'context' => $error_context);
				if($this->_config->debug){
					echo '<pre>';print_r($error);echo '</pre>';
				}
			});
		}

		// handle http requests
		if(!isset($_SERVER['argv']) && isset($_SERVER['HTTP_HOST'])){
			// get router and request object
			$router = new routing\Router();
			$this->_request = routing\Request::instance();

			// register routes
			if(isset($config['routes'])){
				foreach($config['routes'] as $route){
					$router->registerRoute($route['requestMethod'], $route['route'], $route['callback']);
				}
			}
			
			// register controllers
			if(!isset($config['controllers']))
				$config['controllers'] = array();
			$router->registerControllers($config['controllers']);

			// start a session
			$this->_session = new Session();
			
			// resolve the route
			$router->resolveRoute();
		}
		// handle console commands
		else if(isset($_SERVER['argv'], $_SERVER['argv'][0], $_SERVER['argv'][1])){
			$cmd = $_SERVER['argv'][1];
			unset($_SERVER['argv'][0], $_SERVER['argv'][1]);
			$class = '\commands\\' . ucfirst($cmd) . 'Command';
			if(class_exists($class)){
				$command = new $class();
				$command->execute(array_values($_SERVER['argv']));
			} else {
				throw new \Exception('command ' . $cmd . ' not found');
			}
		}
	}

	public function autoload($class){
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
		$found = false;
		foreach($this->_config->sources as $source){
			if(file_exists($this->_config->document_root . $source . $path . '.php')){
				require_once($this->_config->document_root . $source . $path . '.php');
				spl_autoload($class);
				$found = true;
				break;
			}
		}
		return $found;
	}

	public static function encrypt($string){
	    $td = mcrypt_module_open(self::$_instance->_cipher, '', 'ecb', '');
	    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	    mcrypt_generic_init($td, self::$_instance->_encryptionKey, $iv);
	    $encryptedString = mcrypt_generic($td, $string);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return base64_encode($encryptedString);
	}

	public static function decrypt($encryptedString){
		$td = mcrypt_module_open(self::$_instance->_cipher, '', 'ecb', '');
	    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	    mcrypt_generic_init($td, self::$_instance->_encryptionKey, $iv);
	    $string = mdecrypt_generic($td, $encryptedString);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return base64_decode($string);
	}

	public static function __callStatic($method, $parameters){
		if(property_exists(self::$_instance, '_' . $method))
			return self::$_instance->{'_' . $method};
		return false;
	}

	public function __get($property){
		if(property_exists($this, '_' . $property))
			return $this->{'_' . $property};
		return false;
	}

	public function __set($property, $value){
		if(property_exists($this, '_' . $property))
			$this->{'_' . $property} = $value;
	}

	public function __isset($property){
		if(property_exists($this, '_' . $property))
			return true;
		return false;
	}
}

class Config {
	private $_properties;
	public function __construct(Array $config = array()){
		$this->_properties = new StdClass();
		if(isset($config['env'])){
			if(isset($_SERVER['HTTP_HOST'], $config['env'][$_SERVER['HTTP_HOST']])){
				foreach($config['env'][$_SERVER['HTTP_HOST']] as $k => $v)
					$config[$k] = $v;
			}
		}
		unset($config['env']);
		foreach($config as $k => $v){
			if($k != 'controllers' && $k != 'routes'){
				$k = str_replace('.', '_', $k);
				$this->_properties->$k = $v;
			}
		}
	}
	public function __get($property){
		$property = str_replace('.', '_', $property);
		if(property_exists($this->_properties, $property))
			return $this->_properties->$property;
		return null;
	}
	public function __set($property, $value){
		$property = str_replace('.', '_', $property);
		$this->_properties->$property = $value;
	}
	public function __isset($property){
		$property = str_replace('.', '_', $property);
		if(property_exists($this->_properties, $property))
			return true;
		return false;
	}
}

class Session {
	public function __construct(){
		session_start();
	}
	public function __get($property){
		if(isset($_SESSION[$property]))
			return unserialize($_SESSION[$property]);
		return null;
	}
	public function __set($property, $value){
		$_SESSION[$property] = serialize($value);
	}
	public function __isset($property){
		if(isset($_SESSION[$property]))
			return true;
		return false;
	}
}