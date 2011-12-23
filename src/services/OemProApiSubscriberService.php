<?php
class OemProApiSubscriberService extends OemProApiService {
	public function __construct(OemProApiClient &$client) {
		parent::__construct('Subscriber', $client);
	}
	
	public function Get($listID, $emailAddress) {
		return $this->call('Get', array(
			'ListID' => intval($listID),
			'EmailAddress' => $emailAddress
		), true);
	}
	
	/**
	 * 
	 * Updates subscriber information.
	 * The userData argument can use the following keys:
	 * - EmailAddress
	 * - Fields: an array where keys are custom field IDs, and values are the values of the related custom field
	 * 
	 * @param int $subscriberID the subscriber's ID
	 * @param int $subscriberListID the subscriber's list ID
	 * @param array $userData the data to be updated 
	 */
	public function Update($subscriberID, $subscriberListID, $userData) {
		$params = array(
			'SubscriberID' => intval($subscriberID),
			'SubscriberListID' => intval($subscriberListID)
		);
		
		if(isset($userData['EmailAddress'])) $params['EmailAddress'] = $userData['EmailAddress'];
		if(isset($userData['Fields'])) {
			if(!is_array($userData['Fields'])) throw new Exception('Fields must be an array');
			foreach($userData['Fields'] as $fieldID => $fieldValue) {
				$params['Fields[CustomField' . $fieldID . ']'] = $fieldValue;
			}
		}
		
		return $this->call('Update', $params, true);
	}
	
	public function Subscribe($listID, $emailAddress, $fields = array(), $ipAddress = false) {
		if(empty($ipAddress)) {
			if(isset($_SERVER['REMOTE_ADDR'])) $ipAddress = $_SERVER['REMOTE_ADDR'];
			else $ipAddress = '127.0.0.1';
		}
		
		$params = array(
			'ListID' => intval($listID),
			'EmailAddress' => $emailAddress,
			'IPAddress' => $ipAddress
		);
		
		foreach($fields as $fieldID => $fieldValue) {
			$params['CustomField' . $fieldID] = $fieldValue;
		}
		
		return $this->call('Subscribe', $params, true);
	}
	
	protected function getErrorMessage($subCommand, $errorCode, $response) {
		if($subCommand == 'Subscribe') {
			switch($errorCode) {
				case 1: return 'Missing ListID parameter';
				case 2: return 'Missing EmailAddress parameter';
				case 3: return 'Missing IPAddress parameter';
				case 4: return 'Invalid ListID';
				case 11: return 'Invalid User';
				case 5: return 'Invalid EmailAddress';
				case 6: 
					$message = 'Missing required custom field';
					if(isset($response['ErrorCustomFieldID'])) $message .= '. Field ID: ' . $response['ErrorCustomFieldID'];
					if(isset($response['ErrorCustomFieldTitle'])) $message .= '. Field title: ' . $response['ErrorCustomFieldTitle'];
					return $message;
				case 8:
					$message = 'Invalid custom field';
					if(isset($response['ErrorCustomFieldID'])) $message .= '. Field ID: ' . $response['ErrorCustomFieldID'];
					if(isset($response['ErrorCustomFieldTitle'])) $message .= '. Field title: ' . $response['ErrorCustomFieldTitle'];
					if(isset($response['ErrorCustomFieldDescription'])) $message .= '. Field description: ' . $response['ErrorCustomFieldDescription'];
					return $message;
				case 7:
					$message = 'Custom field value must be unique';
					if(isset($response['ErrorCustomFieldID'])) $message .= '. Field ID: ' . $response['ErrorCustomFieldID'];
					if(isset($response['ErrorCustomFieldTitle'])) $message .= '. Field title: ' . $response['ErrorCustomFieldTitle'];
					return $message;
				case 9: return 'Subscriber already exists';
				case 10: return 'An error occurred while adding the subscriber to the list';
			}
		}
		else if($subCommand == 'Get') {
			switch($errorCode) {
				case 1: return 'Missing EmailAddress parameter';
				case 2: return 'Missing ListID parameter';
				case 3: return 'Subscriber does not exist';
			}
		}
		else if($subCommand == 'Update') {
			switch($errorCode) {
				case 1: return 'Missing SubscriberID parameter';
				case 2: return 'Missing SubscriberListID parameter';
				case 5: return 'Invalid List';
				case 6: return 'Invalid Subscriber';
				case 4: return 'Invalid EmailAddress';
				case 8:
					$message = 'Missing required custom fields';
					if(isset($response['ErrorCustomFieldIDs'])) $message .= '. Field IDs: ' . $response['ErrorCustomFieldIDs'];
					if(isset($response['ErrorCustomFieldTitles'])) $message .= '. Field titles: ' . $response['ErrorCustomFieldTitles'];
					return $message;
				case 9:
					$message = 'Some custom field values must be unique';
					if(isset($response['ErrorCustomFieldIDs'])) $message .= '. Field IDs: ' . $response['ErrorCustomFieldIDs'];
					if(isset($response['ErrorCustomFieldTitles'])) $message .= '. Field titles: ' . $response['ErrorCustomFieldTitles'];
					return $message;
				case 10:
					$message = 'Invalid custom fields';
					if(isset($response['ErrorCustomFieldIDs'])) $message .= '. Field IDs: ' . $response['ErrorCustomFieldIDs'];
					if(isset($response['ErrorCustomFieldTitles'])) $message .= '. Field titles: ' . $response['ErrorCustomFieldTitles'];
					if(isset($response['ErrorCustomFieldDescriptions'])) $message .= '. Field descriptions: ' . $response['ErrorCustomFieldDescriptions'];
					return $message;
				case 7: return 'Duplicate';
			}
		}
		
		return null;
	}
}