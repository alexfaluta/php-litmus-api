<?php
	//Include Litmus class
	include_once 'class.litmus.php';
	
	//Create new Litmus
	$litmus = new Litmus('subdomeniu', 'username', 'password');
	
	//Create new email test
	$clients = $litmus->emails_clients();
	echo $litmus->emails_create($clients, 'Lorem ipsum with <strong>strong</strong>, <em>italic</em> and <a href="#">link</a>', 'Hellow world');
	
	//Create new page test
	$clients = $litmus->pages_clients();
	echo $litmus->pages_create($clients, 'http://alex-faluta.net');
?>