<?php

//require 'vendor/autoload.php';
//require_once 'config.php';

use JiraRestApi\HTTPException;

class CurlTest extends PHPUnit_Framework_TestCase 
{
	public function testCurlPost()
	{
		try {	

			$j = new \JiraRestApi\JiraClient($jira_config, $options);

			$post_data = array("name" => "value");

			$http_status = 0;
			$ret = $j->exec('/abcd', json_encode($post_data), $http_status);

			var_dump($ret);
			$this->assertTrue(TRUE);
		} catch (HTTPException $e) {
			var_dump($e);
			$this->assertTrue(FALSE);
		}
	}
}
?>