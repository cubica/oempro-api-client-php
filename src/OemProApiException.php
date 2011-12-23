<?php
class OemProApiException extends Exception {
	private $errors = array();
	private $command;
	private $request;
	private $response;
	
	public function __construct($command, $request, $response) {
		parent::__construct('An error occurred while calling the OemPro API');
		$this->command = $command;
		$this->request = $request;
		$this->response = $response;
	}
	
	public function getCommand() {
		return $this->command;
	}
	
	public function getRequest() {
		return $this->request;
	}
	
	public function getResponse() {
		return $this->response;
	}
	
	public function addError($code, $message) {
		$this->errors[$code] = $message;
	}
	
	public function getErrors() {
		return $this->errors;
	}
	
	public function __toString() {
		$output = parent::__toString();
		$output .= sprintf("\nCommand: %s\nRequest: %s\nResponse: %s", $this->command, print_r($this->request, true), print_r($this->response, true));
		if(!empty($this->errors)) {
			$output .= "\nErrors:";
			foreach($this->errors as $code => $message) {
				$output .= sprintf("\n\t%s: %s", $code, $message);	
			}
		}
		
		return $output;
	}
}