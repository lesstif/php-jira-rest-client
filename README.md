# PHP JIRA Rest Client

[![Latest Stable Version](https://poser.pugx.org/lesstif/php-jira-rest-client/v/stable)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Latest Unstable Version](https://poser.pugx.org/lesstif/php-jira-rest-client/v/unstable)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![License](https://poser.pugx.org/lesstif/php-jira-rest-client/license)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Total Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/downloads)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Monthly Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/d/monthly)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Daily Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/d/daily)](https://packagist.org/packages/lesstif/php-jira-rest-client)

# Requirements

- PHP >= 5.4.9
- [php JsonMapper](https://github.com/netresearch/jsonmapper)
- [phpdotenv](https://github.com/vlucas/phpdotenv)

# Installation

1. Download and Install PHP Composer.

	``` sh
	curl -sS https://getcomposer.org/installer | php
	```

2. Next, run the Composer command to install the latest version of php jira rest client.
	``` sh
	php composer.phar require lesstif/php-jira-rest-client "^1.7.0"
	```
    or add the following to your composer.json file.
	```json
	{
	    "require": {
	        "lesstif/php-jira-rest-client": "^1.7.0"
	    }
	}
	```
	**Note:**
	If you are using **laravel 5.0 or 5.1**(this version dependent on phpdotenv 1.x), then use **"1.5.\*"** version instead.

3. Then run Composer's install or update commands to complete installation. 

	```sh
	php composer.phar install
	```
	
4. After installing, you need to require Composer's autoloader:

	```php
	require 'vendor/autoload.php';
	```

# Configuration

you can choose loads environment variables either 'dotenv' or 'array'.

## use dotenv


copy .env.example file to .env on your project root.	
	
	JIRA_HOST="https://your-jira.host.com"
	JIRA_USER="jira-username"
	JIRA_PASS="jira-password"

**important-note:** If you are using previous versions(a prior v1.2), you should move config.jira.json to .env and will edit it. 

If you are developing with laravel framework(5.x), you must append above configuration to your application .env file.

## use array

create Service class with ArrayConfiguration parameter.

```php
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;

$iss = new IssueService(new ArrayConfiguration(
          array(
               'jiraHost' => 'https://your-jira.host.com',
               'jiraUser' => 'jira-username',
               'jiraPassword' => 'jira-password',
          )
   ));
```

# Usage

## Table of Contents

### Project
- [Get Project Info](#get-project-info)
- [Get All Project list](#get-all-project-list)

### Custom Field
- [Get All Field list](#get-all-field-list)
- [Create Custom Field](#create-custom-field)

### Issue
- [Get Issue Info](#get-issue-info)
- [Create Issue](#create-issue)
- [Create Issue - bulk](#create-multiple-issue)
- [Create Sub Task](#create-sub-task)
- [Add Attachment](#add-attachment)
- [Update issue](#update-issue)
- [Add comment](#add-comment)
- [Perform a transition on an issue](#perform-a-transition-on-an-issue)
- [Perform an advanced search, using the JQL](#perform-an-advanced-search)
- [Issue time tracking](#issue-time-tracking)
- [Add worklog in Issue](#add-worklog-in-issue)
- [Edit worklog in Issue](#edit-worklog-in-issue)
- [Get Issue worklog](#get-issue-worklog)

### User
- [Get User Info](#get-user-info)

#### Get Project Info

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
	$proj = new ProjectService();

	$p = $proj->get('TEST');
	
	print_r($p);			
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Get All Project list

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
	$proj = new ProjectService();

	$prjs = $proj->getAllProjects();

	foreach ($prjs as $p) {
		echo sprintf("Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n",
			$p->key, $p->id, $p->name, $p->projectCategory['name']
			);
			
	}			
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Get All Field List

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Field\Field;
use JiraRestApi\Field\FieldService;
use JiraRestApi\JiraException;

try {
    $fieldService = new FieldService();

	 // return custom field only. 
    $ret = $fieldService->getAllFields(Field::CUSTOM); 
    	
    Dumper::dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}

```

#### Create Custom Field

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Field\Field;
use JiraRestApi\Field\FieldService;
use JiraRestApi\JiraException;

try {
    $field = new Field();
    
    $field->setName("New custom field")
            ->setDescription("Custom field for picking groups")
            ->setType("com.atlassian.jira.plugin.system.customfieldtypes:grouppicker")
            ->setSearcherKey("com.atlassian.jira.plugin.system.customfieldtypes:grouppickersearcher");

    $fieldService = new FieldService();

    $ret = $fieldService->create($field);
    Dumper::dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'Field Create Failed : '.$e->getMessage());
}

```


#### Get Issue Info

Returns a full representation of the issue for the given issue key.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

try {
	$issueService = new IssueService();
	
   $queryParam = [
            'fields' => [  // default: '*all'
                'summary',
                'comment',
            ],
            'expand' => [
                'renderedFields',
                'names',
                'schema',
                'transitions',
                'operations',
                'editmeta',
                'changelog',
            ]
        ];
            
	$issue = $issueService->get('TEST-867', $queryParam);
	
	print_r($issue->fields);	
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Create Issue

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

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
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Create Multiple Issue

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

try {
    $issueFieldOne = new IssueField();

    $issueFieldOne->setProjectKey("TEST")
                ->setSummary("something's wrong")
                ->setPriorityName("Critical")
                ->setIssueType("Bug")
                ->setDescription("Full description for issue");

    $issueFieldTwo = new IssueField();

    $issueFieldTwo->setProjectKey("TEST")
                ->setSummary("something else is wrong")
                ->setPriorityName("Critical")
                ->setIssueType("Bug")
                ->setDescription("Full description for second issue");
    
    $issueService = new IssueService();

    $ret = $issueService->createMultiple([$issueFieldOne, $issueFieldTwo]);
    
    //If success, returns an array of the created issues
    print_r($ret);
} catch (JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Create Sub Task

Creating a sub-task is similar to creating a regular issue, with two important method calls:

```php
->setIssueType('Sub-task')
->setParentKeyOrId($issueKeyOrId)
```

for example
                
```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

try {
	$issueField = new IssueField();

	$issueField->setProjectKey("TEST")
				->setSummary("something's wrong")
				->setAssigneeName("lesstif")
				->setPriorityName("Critical")
				->setDescription("Full description for issue")
				->addVersion("1.0.1")
				->addVersion("1.0.3")
				->setIssueType("Sub-task")  //issue type must be Sub-task
				->setParentKeyOrId('TEST-143')  //Issue Key
				;

	$issueService = new IssueService();

	$ret = $issueService->create($issueField);

	//If success, Returns a link to the created sub task.
	print_r($ret);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Add Attachment

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

$issueKey = "TEST-879";

try {
    $issueService = new IssueService();

    // multiple file upload support.
    $ret = $issueService->addAttachments($issueKey, 
    	array('screen_capture.png', 'bug-description.pdf', 'README.md'));

    print_r($ret);
} catch (JiraException $e) {
    $this->assertTrue(FALSE, "Attach Failed : " . $e->getMessage());
}

```

#### Update issue

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

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

} catch (JiraException $e) {
	$this->assertTrue(FALSE, "update Failed : " . $e->getMessage());
}

```

#### Add comment

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Comment;
use JiraRestApi\JiraException;

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
} catch (JiraException $e) {
	$this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
}

```

#### Perform a transition on an issue

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;
use JiraRestApi\JiraException;

$issueKey = "TEST-879";

try {			
	$transition = new Transition();
	$transition->setTransitionName('Resolved');
	$transition->setCommentBody('performing the transition via REST API.');

	$issueService = new IssueService();

	$issueService->transition($issueKey, $transition);
} catch (JiraException $e) {
	$this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
}

```

#### Perform an advanced search

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';

try {
    $issueService = new IssueService();

    $ret = $issueService->search($jql);
    var_dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}

```

#### Issue time tracking

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\TimeTracking;
use JiraRestApi\JiraException;

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
} catch (JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}

```

#### Add worklog in issue

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraException;

$issueKey = 'TEST-961';

try {
    $workLog = new Worklog();

    $workLog->setComment('I did some work here.')
        ->setStarted("2016-05-28 12:35:54")
        ->setTimeSpent('1d 2h 3m');

    $issueService = new IssueService();

    $ret = $issueService->addWorklog($issueKey, $workLog);

    $workLogid = $ret->{'id'};

    var_dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
}

```

#### edit worklog in issue

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraException;

$issueKey = 'TEST-961';
$workLogid = '12345';

try {
    $workLog = new Worklog();

    $workLog->setComment('I did edit previous worklog here.')
        ->setStarted("2016-05-29 13:15:34")
        ->setTimeSpent('3d 4h 5m');

    $issueService = new IssueService();

    $ret = $issueService->updateWorklog($issueKey, $workLog, $workLogid);

    var_dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'Edit worklog Failed : '.$e->getMessage());
}

```

#### Get issue worklog

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraException;

$issueKey = 'TEST-961';

try {
    $issueService = new IssueService();
    
    // get issue's all worklog
    $worklogs = $issueService->getWorklog($issueKey)->getWorklogs();
    var_dump($worklogs);
    
    // get worklog by id
    $wlId = 12345;
    $wl = $issueService->getWorklogById($issueKey, $wlId);
    var_dump($wl);
    
} catch (JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}

```

#### Get User Info

Returns a user.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;

try {
	$us = new UserService();

	$user = $us->get(['username' => 'lesstif']);

	var_dump($user);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

# License

Apache V2 License

# JIRA Rest API Documents
* 6.4 - https://docs.atlassian.com/jira/REST/6.4/
* latest - https://docs.atlassian.com/jira/REST/latest/
