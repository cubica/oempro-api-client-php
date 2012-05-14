<?php
class OemProApiSubscribersService extends OemProApiService {
	public function __construct(OemProApiClient &$client) {
		parent::__construct('Subscribers', $client);
	}
	
	public function Get($listId, $recordsFrom, $recordsPerRequest, $orderField = 'SubscriberID', $orderType = 'ASC', $searchField = '', $searchKeyword = '', $segmentId = 'Active') {
		return $this->call('Get', array(
			'OrderField' => $orderField,
			'OrderType' => (!empty($orderType) && strtoupper($orderType) == 'DESC')?'DESC':'ASC',
			'RecordsFrom' => intval($recordsFrom),
			'RecordsPerRequest' => intval($recordsPerRequest),
			'SearchField' => $searchField,
			'SearchKeyword' => $searchKeyword,
			'SubscriberListID' => $listId,
			'SubscriberSegment' => $segmentId
		), true);
	}
	
	public function Count($listId) {
		$getResult = $this->Get($listId, 0, 1);
		return intval($getResult['TotalSubscribers']);
	}
	
	public function GetAll($listId, $orderField = 'SubscriberID', $orderType = 'ASC', $searchField = '', $searchKeyword = '', $segmentId = 'Active') {
		$count = $this->Count($listId);
		return $this->Get($listId, 0, $count, $orderField, $orderType, $searchField, $searchKeyword, $segmentId);
	}
	
	protected function getErrorMessage($subCommand, $errorCode, $response) {
		if($subCommand == 'Get') {
			switch($errorCode) {
				case 1: return 'Missing subscriber list ID';
				case 2: return 'Target segment ID is missing';
				case 99998: return 'Authentication failure or session expired';
				case 99999: return 'Not enough privileges';
			}
		}
		
		return null;
	}
}