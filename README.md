
# Installation

```
composer require lesstif/php-jira-rest-client dev-master
```

create config.jira.json file on your project root.
````json
{
    "host": "https://jira.example.com",
    "username": "username",
    "password": "password",
    "CURLOPT_SSL_VERIFYHOST": false,
    "CURLOPT_SSL_VERIFYPEER": false,
    "CURLOPT_VERBOSE": false,
    "LOG_FILE": "jira-rest-client.log",
    "LOG_LEVEL": "DEBUG"
}
````

# Usage

## Table of Contents
- [Get Project Info](#Get-Project-Info)
- [Get All Project list](#Get-All-Project-list)
- [Get Issue Info](#Get-Issue-Info)
- [Create Issue](#Create-Issue)
- [Add Attachment](#Add-Attachment)
- [Update issue](#Update-issue)
- [Add comment](#Add-comment)
- [Perform a transition on an issue](#Perform a transition on an issue)

## Get Project Info

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;

try {
	$proj = new ProjectService();

	$p = $proj->get('TEST');
	
	print_r($p);			
} catch (JIRAException $e) {
	print("Error Occured! " . $e->getMessage());
}
?>
````

## Get All Project list
````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;

try {
	$proj = new ProjectService();

	$prjs = $proj->getAllProjects();

	foreach ($prjs as $p) {
		echo sprintf("Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n",
			$p->key, $p->id, $p->name, $p->projectCategory['name']
			);
			
	}			
} catch (JIRAException $e) {
	print("Error Occured! " . $e->getMessage());
}
?>
````

## Get Issue Info

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
try {
	$issueService = new IssueService();

	$issue = $issueService->get('TEST-867');
	
	print_r($issue->fields);	
} catch (JIRAException $e) {
	print("Error Occured! " . $e->getMessage());
}

?>
````

## Create Issue

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
try {
	$issueField = new IssueField();
	$issueField->setProjectKey("TEST")
				->setSummary("something's wrong")
				->setAssigneeName("lesstif")
				->setPriorityName("Critical")
				->setIssueType("Bug")
				->setDescription("Full description for issue")
				->addVersion("1.0.1")
				->addVersion("1.0.3");
	
	$issueService = new IssueService();

	$ret = $issueService->create($issueField);
	
	//If success, Returns a link to the created issue.
	print_r($ret);
} catch (JIRAException $e) {
	print("Error Occured! " . $e->getMessage());
}

?>
````

## Add Attachment

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
try {
    $issueService = new IssueService();

    $ret = $issueService->addAttachments("TEST-879", 'screen_capture.png');

    print_r($ret);
} catch (JIRAException $e) {
    $this->assertTrue(FALSE, "Attach Failed : " . $e->getMessage());
}

?>
````

## Update issue

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;

$issueKey = "TEST-920";

//$this->markTestIncomplete();
try {			
	$issueField = new IssueField(true);

	$issueField->setAssigneeName("admin")
				->setPriorityName("Blocker")
				->setIssueType("Task")
				->addLabel("test-label-first")
				->addLabel("test-label-second")
				->addVersion("1.0.1")
				->addVersion("1.0.2")
				->setDescription("This is a shorthand for a set operation on the summary field")
				;

	$issueService = new IssueService();

	$ret = $issueService->update($issueKey, $issueField);


} catch (JIRAException $e) {
	$this->assertTrue(FALSE, "update Failed : " . $e->getMessage());
}

?>
````

## Add comment

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Comment;

$issueKey = "TEST-879";

try {			
	$comment = new Comment();

	$body = <<<COMMENT
Adds a new comment to an issue.
* Bullet 1
* Bullet 2
** sub Bullet 1
** sub Bullet 2
* Bullet 3
COMMENT;
	$comment->setBody($body)
		->setVisibility('role', 'Users');
	;

	$issueService = new IssueService();
	$ret = $issueService->addComment($issueKey, $comment);
	print_r($ret);
} catch (JIRAException $e) {
	$this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
}

?>
````

## Perform a transition on an issue

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;

$issueKey = "TEST-879";

try {			
	$transition = new Transition();
	$transition->setTransitionName('Resolved');
	$transition->setCommentBody('performing the transition via REST API.');

	$issueService = new IssueService();

	$issueService->transition($issueKey, $transition);
} catch (JIRAException $e) {
	$this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
}

?>
````

# License

Apache V2 License

# JIRA Rest API Documents
* 6.2 - https://docs.atlassian.com/jira/REST/6.2/
* latest - https://docs.atlassian.com/jira/REST/latest/
