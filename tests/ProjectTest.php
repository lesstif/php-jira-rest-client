<?php

require 'vendor/autoload.php';
require_once 'config.jira.php';

use JiraRestApi\Project\ProjectService;

class ProjectTest extends PHPUnit_Framework_TestCase 
{
    public function testProjectLists()
    {
		try {
			$proj = new ProjectService($jira_config, $options);

			//$ret = $proj->getAllProjects();

			/*
			$mapper = new JsonMapper();
			$mapper->bExceptionOnUndefinedProperty = true;

			$prjs = $mapper->mapArray(
		   		 json_decode($ret), new ArrayObject(), '\JiraRestApi\Project\Project'
			);
			var_dump($prjs);
			*/
			$p = $proj->get('BITA');
			var_dump($p);

			//$project = $mapper->map($ret, new Project());
			//var_dump($project);
		} catch (HTTPException $e) {
			var_dump($e);
		}
	}
	//
}

?>
