
# Installation

```
composer require lesstif/php-jira-rest-client dev-master
```

create config.jira.php file on your project root.
````php
<?php

/*
 * Description get Jira Host Configuration
 * 
 * @return array 
 */
function getHostConfig() {
	$jira_config = array ('host' => 'https://jira.example.com',
			'username' => 'username',
			'password' => 'secure_passwd');

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
		CURLOPT_VERBOSE => false,
		'LOG_FILE' => 'jira-rest-client.log',
		'LOG_LEVEL' => \Monolog\Logger::INFO
		);

	return $options;
}
?>
````

# Usage

## Get Project Info

````php
try {
	$proj = new ProjectService();

	$p = $proj->get('TEST');
	
	print_r($p);			
} catch (HTTPException $e) {
	print("Error Occured! " . $e->getMessage());
}
````

## Get All Project list
````php
try {
	$proj = new ProjectService();

	$prjs = $proj->getAllProjects();

	$i = 0;
	foreach ($prjs as $p) {
		echo sprintf("Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n",
			$p->key, $p->id, $p->name, $p->projectCategory['name']
			);
			
	}			
} catch (HTTPException $e) {
	print("Error Occured! " . $e->getMessage());
}
````

## Get Issue Info

````php
try {
	$issueService = new IssueService();

	$issue = $issueService->get('TEST-867');
	
	print_r($issue->fields);	
} catch (HTTPException $e) {
	print("Error Occured! " . $e->getMessage());
}
````

## Create Issue

````php
try {
	$issueField = new IssueField();
	$issueField->setProjectId("12000");
	$issueField->setSummary("something's wrong");
	$issueField->setAssigneeName("lesstif");
	$issueField->setPriorityName("Critical");
	$issueField->setIssueType("Bug");
	$issueField->setDescription("Full description for issue");

	$issueField->addVersion(null, "1.0.1");
	$issueField->addVersion(null, "1.0.3");
	
	$issueService = new IssueService();

	$ret = $issueService->create($issueField);
	
	//If success, Returns a link to the created issue.
	print_r($ret);
} catch (HTTPException $e) {
	print("Error Occured! " . $e->getMessage());
}
````

# License

Apache V2 License

# JIRA Rest API Documents
* 6.2 - https://docs.atlassian.com/jira/REST/6.2/
* latest - https://docs.atlassian.com/jira/REST/latest/

