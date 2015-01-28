<?php

require 'vendor/autoload.php';
require_once 'config.jira.php';

use JiraRestApi\Project\ProjectService;

try {
	$proj = new ProjectService($jira_config, $options);

	$ret = $proj->getAllProjects();

	var_dump($ret);
} catch (HTTPException $e) {
	var_dump($e);
}

?>