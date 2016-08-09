<?php
	//Include Litmus class
	include_once 'class.litmus.php';
	
	//Create new Litmus
	$litmus = new Litmus('subdomain', 'username', 'password');
	
	//Returns details for all tests.
	echo $litmus->tests();
	
	//Returns details of a single test.
	echo $litmus->tests_show($id);
	
	//?Updates a test in your account. This is used for publishing results publicly or changing a test's title.
	echo $litmus->tests_update($id, $name, $public_sharing);
	
	//Returns details of a single version for a particular test.
	echo $litmus->versions_show($id, $version);
	
	//Creates a new version of a test.
	echo $litmus->versions_create($id);
	
	//To reduce the strain on the Litmus servers, and to reduce the bandwidth you use to check for test completion, there is a special poll method that can be used.
	echo $litmus->versions_poll($id, $version);
	
	//Used to retrieve details of a single result.
	echo $litmus->results_show($id, $version, $result_id);
	
	//?This method is used to update the properties of a result.
	//?echo $litmus->results_update($id, $version, $result_id, $state);
	
	//Triggers a retest of just this client.
	echo $litmus->results_retest($id, $version, $result_id);
	
	//Creates a new web page test in your account.
	echo $litmus->pages_create($litmus->pages_clients(), $url);
	
	//Returns a list of web browsers available for testing.
	echo $litmus->pages_clients();
	
	//Creates a new email test in your account.
	echo $litmus->emails_create($litmus->emails_clients(), $body, $subject);
	
	//Returns a list of email clients available for testing.
	echo $litmus->emails_clients();
	
	//Shows a list of the 20 most recent lists you've created within your Litmus account.
	echo $litmus->reports_index();
	
	//Retrieves a single specified report (campaign).
	echo $litmus->reports_show($id);
	
	//Creates a new report if you have available credits.
	echo $litmus->reports_create($name);
	
	//Select images for specific test and store into array
	var_dump($litmus->select_tests_images($id));
?>