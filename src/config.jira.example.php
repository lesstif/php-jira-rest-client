<?php

$jira_config = array ('host' => 'https://jira.example.com',
		'username' => 'username',
		'password' => 'password');

$options = array(
	CURLOPT_SSL_VERIFYHOST => 0,
	CURLOPT_SSL_VERIFYPEER => 0,	
	CURLOPT_VERBOSE => true,
	'LOG_FILE' => 'log/jira-rest-client.log',
	'LOG_LEVEL' => \Monolog\Logger::INFO
	);

?>
