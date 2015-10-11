
# Installation

1. Download and Install PHP Composer.

	``` sh
	curl -sS https://getcomposer.org/installer | php
	```

2. Next, run the Composer command to install the latest version of php jira rest client.
	``` sh
	php composer.phar require lesstif/php-jira-rest-client dev-master
	```
    or add the following to your composer.json file.
	```json
	{
	    "require": {
	        "lesstif/php-jira-rest-client": "dev-master"
	    }
	}
	```
3. Then run Composer's install or update commands to complete installation. 

	```sh
	php composer.phar install
	```
	
4. After installing, you need to require Composer's autoloader:

	```php
	require 'vendor/autoload.php';
	```

# Configuration

copy .env.example file to .env on your project root.	
	
	JIRA_HOST="https://your-jira.host.com"
	JIRA_USER="jira-username"
	JIRA_PASS="jira-password"

**important-note:** If you are using previous versions(a prior v1.2), you should move config.jira.json to .env and will edit it. 

# Usage

## Table of Contents
- [Get Project Info](#get-project-info)
- [Get All Project list](#get-all-project-list)
- [Get Issue Info](#get-issue-info)
- [Create Issue](#create-issue)
- [Add Attachment](#add-attachment)
- [Update issue](#update-issue)
- [Add comment](#add-comment)
- [Perform a transition on an issue](#perform-a-transition-on-an-issue)
- [Perform an advanced search, using the JQL](#perform-an-advanced-search)
- [Issue time tracking](#issue-time-tracking)
- [Issue worklog](#issue-worklog)

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

$issueKey = "TEST-879";

try {
    $issueService = new IssueService();

    // multiple file upload support.
    $ret = $issueService->addAttachments($issueKey, 
    	array('screen_capture.png', 'bug-description.pdf', 'README.md'));

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

$issueKey = "TEST-879";

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

## Perform an advanced search

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;

$jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';

try {
    $issueService = new IssueService();

    $ret = $issueService->search($jql);
    var_dump($ret);
} catch (JIRAException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
?>
````

## Issue time tracking

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\TimeTracking;

$issueKey = 'TEST-961';

try {
    $issueService = new IssueService();
    
    // get issue's time tracking info
    $ret = $issueService->getTimeTracking($this->issueKey);
    var_dump($ret);
    
    $timeTracking = new TimeTracking;

    $timeTracking->setOriginalEstimate('3w 4d 6h');
    $timeTracking->setRemainingEstimate('1w 2d 3h');
    
    // add time tracking
    $ret = $issueService->timeTracking($this->issueKey, $timeTracking);
    var_dump($ret);
} catch (JIRAException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
?>
````

## Issue worklog

````php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;

$issueKey = 'TEST-961';

try {
    $issueService = new IssueService();
    
    // get issue's worklog
    $worklogs = $issueService->getWorklog($issueKey)->getWorklogs();
    var_dump($ret);    
} catch (JIRAException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
?>
````

# License

Apache V2 License

# JIRA Rest API Documents
* 6.2 - https://docs.atlassian.com/jira/REST/6.2/
* latest - https://docs.atlassian.com/jira/REST/latest/
