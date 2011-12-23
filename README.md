OemPro API PHP client
========

This library provides a PHP client for the Octeth OemPro API. It has been tested with OemPro 4.3.4.
Currently I've only implemented a subset of the available commands; feel free to ask me if you need other commands to be added.

Usage
-----

In order to use the library, you just need to checkout/unpack the src folder where you prefer.

The client throws an OemProApiException when an error occurs or the called command returns an error message.
The OemProApiException class contains various fields that can help you debug the error, such as request and response data, error codes and messages and so on.

A basic usage example of the implemented commands follows: please refer to the code documentation for further details and help.

	<?php
	require 'path_to_the_library/src/OemProApiClient.php';
	
	$client = new OemProApiClient('http://youroemprosite.com/api.php');
	
	// If you need to use commands that need authentication, such as Subscriber.Subscribe, you must login first:
	$client->User->Login('username', 'password', false);
	// this will set the SessionID on the client; it will automatically use it when calling authenticated commands
	
	// get a subscriber
	$listID = 123; // the ID of an existing list
	$customFieldID = 456; // the ID of a custom field for the previous list
	$subscriber = $client->Subscriber->Get($listID, 'subscriber@domain.com');
	print_r($subscriber);
	
	// update an existing subscriber
	$subscriberID = $subscriber['SubscriberInformation']['SubscriberID'];
	$result = $client->Subscriber->Update($subscriberID, $listID, array(
		'EmailAddress' => 'newaddress@newdomain.com',
		'Fields' => array(
			$customFieldID => 'new field value'
		)
	));
	print_r($result);
	
	// create a new subscriber
	$result = $client->Subscriber->Subscribe($listID, 'newsubscriber@domain.com', array(
		$customFieldID => 'field value'
	));
	print_r($result);
	$newSubscriberID = $result['Subscriber']['SubscriberID'];