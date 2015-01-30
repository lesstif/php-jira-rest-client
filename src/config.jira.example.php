<?php

/**
 * Description get Jira Host Configuration
 * 
 * @return array 
 */
function getHostConfig() {
	$jira_config = array ('host' => 'https://jira.example.com',
			'username' => 'username',
			'password' => 'password');

	return $jira_config;
}

/**
 * Description get Client options
 * 
 * @return array
 */
function getOptions() {
	$options = array(
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,	
		CURLOPT_VERBOSE => true,
		'LOG_FILE' => 'log/jira-rest-client.log',
		'LOG_LEVEL' => \Monolog\Logger::INFO
		);

	return $options;
}

?>
