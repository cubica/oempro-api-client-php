<?php
require_once 'OemProApiException.php';
require_once 'OemProApiService.php';

class OemProApiClient {
	const VERSION = '0.1';
	
	private $url;
	private $sessionID;
	
	private static $serviceNames = array(
		'User',
		'Subscriber'
	);
	
	public function __construct($url) {
		if(empty($url) || !is_string($url)) throw new Exception('url cannot be empty');
		$this->url = $url;
		
		$this->initServices();
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function getSessionID() {
		return $this->sessionID;	
	}
	
	public function setSessionID($sessionID) {
		$this->sessionID = $sessionID;
	}
	
	private function initServices() {
		foreach(self::$serviceNames as $serviceName) {
			$serviceClass = 'OemProApi' . $serviceName . 'Service';
			$serviceFile = 'services' . DIRECTORY_SEPARATOR . $serviceClass . '.php';
			
			require_once $serviceFile;
			
			if(!class_exists($serviceClass)) throw new Exception(sprintf('Cannot initialize service %s: class %s not found', $serviceName, $serviceClass));
			
			$this->{$serviceName} = new $serviceClass($this);
		}
	}
}