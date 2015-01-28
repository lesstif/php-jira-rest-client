<?php

require 'vendor/autoload.php';

require_once 'config.php';

use JiraRestApi\HTTPException;

try {	

	$j = new \JiraRestApi\JiraClient($jira_config, $options);

	$post_data = array("name" => "value");

	$http_status = 0;
	$ret = $j->exec('/abcd', json_encode($post_data), $http_status);

	var_dump($ret);
} catch (HTTPException $e) {
	var_dump($e);
}

?>