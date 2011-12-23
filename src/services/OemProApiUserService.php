<?php
class OemProApiUserService extends OemProApiService {
	public function __construct(OemProApiClient &$client) {
		parent::__construct('User', $client);
	}
	
	/**
	 * Performs login with provided credentials, and sets the SessionID on the client
	 * @param string $username the username
	 * @param string $password the password
	 * @param string $captcha if false, disables captcha verification; if string, is sent as the captcha; if true, captcha is neither sent nor disabled, and an error will occur if captcha verification is enabled in oempro. Defaults to true. 
	 * @throws Exception
	 */
	public function Login($username, $password, $captcha = true) {
		$params = array(
			'Username' => $username,
			'Password' => $password
		);
		
		if($captcha === false) {
			$params['DisableCaptcha'] = 'true';
		}
		else if(is_string($captcha)) $params['Captcha'] = $captcha;
		
		$response = $this->call('Login', $params);
		
		if(!isset($response['SessionID'])) throw new Exception('No SessionID in response');
		$sessionID = $response['SessionID'];
		$this->client->setSessionID($sessionID);
		
		return $response;
	}
	
	protected function getErrorMessage($subCommand, $errorCode, $response) {
		if($subCommand == 'Login') {
			switch($errorCode) {
				case 1: return 'Missing Username parameter';
				case 2: return 'Missing Password parameter';
				case 5: return 'Invalid Captcha';
				case 3: return 'Invalid login information';
			}	
		}
		
		return null;
	}
}