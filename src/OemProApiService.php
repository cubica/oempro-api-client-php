<?php
abstract class OemProApiService {
	protected $client;
	protected $name;
	
	public function __construct($name, OemProApiClient &$client) {
		$this->name = $name;
		$this->client = &$client;
	}
	
	protected function call($subCommand, $params, $useSession = false) {
		$command = $this->name . '.' . $subCommand;
		
		$postData = array(
			'Command' => $command,
			'ResponseFormat' => 'JSON'
		);
		
		if($useSession) {
			$sessionID = $this->client->getSessionID();
			if(empty($sessionID)) throw new Exception(sprintf('Command %s requires authentication, but Session ID has not been initialized', $command));
			
			$postData['SessionID'] = $sessionID;
		}
		
		if(empty($params)) $params = array();
		if(!is_array($params)) $params = array($params);
		$postData = array_merge($params, $postData);
		
		$postDataEncoded = array();
		foreach($postData as $key => $value) {
			$postDataEncoded[] = urlencode($key) . '=' . urlencode($value);
		}
		$postDataEncoded = implode('&', $postDataEncoded);
		
		$url = $this->client->getUrl();
		
		$ch = curl_init($url);
		
		curl_setopt_array($ch, array(
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postDataEncoded
		));
		
		$response = curl_exec($ch);
		$errorCode = curl_errno($ch);
		$errorMessage = curl_error($ch);
		curl_close($ch);
		
		if($errorCode) {
			$ex = new OemProApiException($command, $postData, $response);
			$ex->addError($errorCode, $errorMessage);
			throw $ex;
		}
		
		$jsonResponse = json_decode($response, true);
		
		if(!isset($jsonResponse) || !is_array($jsonResponse) || !isset($jsonResponse['Success']) || !$jsonResponse['Success']) {
			$ex = new OemProApiException($command, $postData, $response);
			
			$errorMessages = array();
			if(isset($jsonResponse['ErrorText'])) {
				$errorMessages = $jsonResponse['ErrorText'];
				if(!is_array($errorMessages)) $errorMessages = array($errorMessages);
			}
			
			$errors = array();
			if(isset($jsonResponse['ErrorCode'])) {
				$errorCodes = $jsonResponse['ErrorCode'];
				if(!is_array($errorCodes)) $errorCodes = array($errorCodes);
				for($i = 0; $i < count($errorCodes); $i++) {
					$errorCode = $errorCodes[$i];
					if(isset($errorMessages[$i])) $errorMessage = $errorMessages[$i];
					else {
						$errorMessage = $this->getErrorMessage($subCommand, $errorCode, $jsonResponse);
						if(empty($errorMessage)) $errorMessage = 'unknown';
					}
					
					$ex->addError($errorCodes[$i], $errorMessage);
				}
			}
			
			throw $ex;
		}
		
		return $jsonResponse;
	}
	
	protected function getErrorMessage($subCommand, $errorCode, $response) {
		return null;
	}
}