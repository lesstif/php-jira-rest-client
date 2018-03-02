# PHP JIRA Rest Client

[![StyleCI](https://styleci.io/repos/30015369/shield?branch=master&style=flat)](https://styleci.io/repos/30015369)
[![Latest Stable Version](https://poser.pugx.org/lesstif/php-jira-rest-client/v/stable)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Latest Unstable Version](https://poser.pugx.org/lesstif/php-jira-rest-client/v/unstable)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![License](https://poser.pugx.org/lesstif/php-jira-rest-client/license)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Total Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/downloads)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Monthly Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/d/monthly)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Daily Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/d/daily)](https://packagist.org/packages/lesstif/php-jira-rest-client)

# Requirements

- PHP >= 5.5.9
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

```sh
JIRA_HOST="https://your-jira.host.com"
JIRA_USER="jira-username"
JIRA_PASS="jira-password"
# to enable session cookie authorization
# COOKIE_AUTH_ENABLED=1
```

Or for OAuth authorization:

```sh
JIRA_ACCESS_TOKEN="access-token"
```

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
               // for basic authorization:
               'jiraUser' => 'jira-username',
               'jiraPassword' => 'jira-password',
               // to enable session cookie authorization (with basic authorization only)
               'cookieAuthEnabled' => true,
               // for OAuth authorization:
               'oauthAccessToken' => 'access-token',
          )
   ));
```

# Usage

## Table of Contents

### Project
- [Get Project Info](#get-project-info)
- [Get All Project list](#get-all-project-list)
- [Get Project Type](#get-project-type)
- [Get Project Version](#get-project-version)

### Custom Field
- [Get All Field list](#get-all-field-list)
- [Create Custom Field](#create-custom-field)

### Issue
- [Get Issue Info](#get-issue-info)
- [Create Issue](#create-issue)
- [Create Issue - bulk](#create-multiple-issues)
- [Create Sub Task](#create-sub-task)
- [Add Attachment](#add-attachment)
- [Update issue](#update-issue)
- [Change assignee](#change-assignee)
- [Remove issue](#remove-issue)
- [Add comment](#add-comment)
- [Perform a transition on an issue](#perform-a-transition-on-an-issue)
- [Perform an advanced search, using the JQL](#perform-an-advanced-search)
    - [Simple JQL](#simple-query)
    - [JQL With pagination](#jql-with-pagination)
    - [Using JQL Query class](#jql-query-class)
- [Remote Issue Link](#remote-issue-link)
    - [Get Remote Issue Link](#get-remote-issue-link)
    - [Create Remote Issue Link](#create-remote-issue-link)
- [Issue time tracking](#issue-time-tracking)
- [Add worklog in Issue](#add-worklog-in-issue)
- [Edit worklog in Issue](#edit-worklog-in-issue)
- [Get Issue worklog](#get-issue-worklog)
- [Add watcher to Issue](#add-watcher-to-issue)
- [Send a notification to the recipients](#issue-notify)

### IssueLink

* [Create Issue Link](#create-issue-link)
* [Get Issue LinkType](#get-issue-linktype)

### User
- [Create User](#create-user)
- [Get User Info](#get-user-info)
- [Find Users](#find-users)
- [Find Assignable Users](#find-assignable-users)

### Group
- [Create Group](#create-group)
- [Get Users from group](#get-users-from-group)
- [Add User to group](#add-user-to-group)

#### Get Project Info

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/project-getProject)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $proj = new ProjectService();

    $p = $proj->get('TEST');
	
    var_dump($p);			
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

#### Get All Project list

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/project-getAllProjects)

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

#### Get Project type

[See Jira API reference (get all types)](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/project/type-getAllProjectTypes)

[See Jira API reference (get type)](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/project/type-getProjectTypeByKey)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $proj = new ProjectService();

    // get all project type
    $prjtyps = $proj->getProjectTypes();

    foreach ($prjtyps as $pt) {
        var_dump($pt);
    }

    // get specific project type.
    $pt = $proj->getProjectType('software');
    var_dump($pt);

} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Get Project Version

get all project's versions.

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/project-getProjectVersions)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\Issue\Version;
use JiraRestApi\JiraException;

try {
    $proj = new ProjectService();

    $vers = $proj->getVersions('TEST');

    foreach ($vers as $v) {
        // $v is  JiraRestApi\Issue\Version
        var_dump($v);
    }
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

or get pagenated project's versions.

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/project-getProjectVersionsPaginated)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $param = [
        'startAt' => 0,
        'maxResults' => 10,
        'orderBy' => 'name',
        //'expand' => null,
    ];

    $proj = new ProjectService();

    $vers = $proj->getVersionsPagenated('TEST', $param);

    foreach ($vers as $v) {
        // $v is  JiraRestApi\Issue\Version
        var_dump($v);
    }
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```


#### Get All Field List

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/field-getFields)

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
    	
    var_dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

#### Create Custom Field

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/field-createCustomField)

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
    
    var_dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'Field Create Failed : '.$e->getMessage());
}
```


#### Get Issue Info

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-getIssue)

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
	
    var_dump($issue->fields);	
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

You can access the custom field associated with issue through *$issue->fields->customFields* array or through direct custom field id variables(Ex: *$issue->fields->customfield_10300*).

#### Create Issue

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-createIssue)

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
                ->addVersion(["1.0.1", "1.0.3"])
                ->addComponents(['Component-1', 'Component-2']);
	
    $issueService = new IssueService();

    $ret = $issueService->create($issueField);
	
    //If success, Returns a link to the created issue.
    var_dump($ret);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

If you want to set custom field, you can call the *addCustomField* function with custom field id and value as parameters.

```php
try {
    $issueField = new IssueField();

    $issueField->setProjectKey("TEST")
                ->setSummary("something's wrong")
                ->setAssigneeName("lesstif")
                ->setPriorityName("Critical")
                ->setIssueType("Bug")
                ->setDescription("Full description for issue")
                ->addVersion("1.0.1")
                ->addVersion("1.0.3")
                ->addCustomField('customfield_10100', 'text area body text']) // String type custom field
                ->addCustomField('customfield_10200', ['value' => 'Linux']) // Select List (single choice)
                ->addCustomField('customfield_10408', [
                    ['value' => 'opt2'], ['value' => 'opt4']
                ]) // Select List (multiple choice)
    ;
	
    $issueService = new IssueService();

    $ret = $issueService->create($issueField);
	
    //If success, Returns a link to the created issue.
    var_dump($ret);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

Currently, not tested for all custom field types.

#### Create Multiple Issues

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-createIssues)

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
    var_dump($ret);
} catch (JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

#### Create Sub Task

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-createIssue)

Creating a sub-task is similar to creating a regular issue, with two important method calls:

```php
->setIssueType('Sub-task')
->setParentKeyOrId($issueKeyOrId)
```

for example
​                
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
    var_dump($ret);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

#### Add Attachment

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue/%7BissueIdOrKey%7D/attachments-addAttachment)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$issueKey = "TEST-879";

try {
    $issueService = new IssueService();

    // multiple file upload support.
    $ret = $issueService->addAttachments($issueKey, 
        ['screen_capture.png', 'bug-description.pdf', 'README.md']
    );

    print_r($ret);
} catch (JiraException $e) {
    $this->assertTrue(FALSE, "Attach Failed : " . $e->getMessage());
}

```

#### Update issue

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-editIssue)

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

    // optionally set some query params
    $editParams = [
        'notifyUsers' => false,
    ];

    $issueService = new IssueService();

    // You can set the $paramArray param to disable notifications in example
    $ret = $issueService->update($issueKey, $issueField, $editParams);

    var_dump($ret);
} catch (JiraException $e) {
	$this->assertTrue(FALSE, "update Failed : " . $e->getMessage());
}
```

If you want to change the custom field type when updating an issue, you can call the *addCustomField* function just as you did for creating issue.

#### Change Assignee

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-assign)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$issueKey = "TEST-879";

try {
	$issueService = new IssueService();

    // if assignee is -1, automatic assignee used.
    // A null assignee will remove the assignee.
    $assignee = 'newAssigneeName';

    $ret = $issueService->changeAssignee($issueKey, $assignee);

    var_dump($ret);
} catch (JiraException $e) {
	$this->assertTrue(FALSE, "Change Assignee Failed : " . $e->getMessage());
}
```

#### Remove Issue

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-deleteIssue)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$issueKey = "TEST-879";

try {
    $issueService = new IssueService();

    $ret = $issueService->deleteIssue($issueKey);
    // if you want to delete issues with sub-tasks
    //$ret = $issueService->deleteIssue($issueKey, array('deleteSubtasks' => 'true'));

    var_dump($ret);
} catch (JiraException $e) {
	$this->assertTrue(FALSE, "Change Assignee Failed : " . $e->getMessage());
}
```

#### Add comment

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-addComment)

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

Note: this library uses goal **status names** instead of **transition names**.
So, if you want to change issue status to 'Some Status',
you should pass that status name to `setTransitionName`

i.e. `$transition->setTransitionName('Some Status')`

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-doTransition)

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

##### Simple Query

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/search-searchUsingSearchRequest)

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

##### JQL with pagination

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/search-search)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';

try {
    $issueService = new IssueService();

    $pagination = -1;
  
    $startAt = 0;	//the index of the first issue to return (0-based)    
    $maxResult = 3;	// the maximum number of issues to return (defaults to 50). 
    $totalCount = -1;	// the number of issues to return
  
    // first fetch
    $ret = $issueService->search($jql, $startAt, $maxResult);
    $totalCount = $ret->total;
  	
    // do something with fetched data
    foreach ($ret->issues as $issue) {
        print (sprintf("%s %s \n", $issue->key, $issue->fields->summary));
    }
  	
    // fetch remained data
    $page = $totalCount / $maxResult;

    for ($startAt = 1; $startAt < $page; $startAt++) {
        $ret = $issueService->search($jql, $startAt, $maxResult);

        print ("\nPaging $startAt\n");
        print ("-------------------\n");
        foreach ($ret->issues as $issue) {
            print (sprintf("%s %s \n", $issue->key, $issue->fields->summary));
        }
    }     
} catch (JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

##### JQL query class

[See Jira API reference](https://confluence.atlassian.com/jiracoreserver/advanced-searching-939937709.html)

If you're not familiar JQL then you can use convenience JqlQuery class.
JqlFunction class can be used to add jql functions calls to query.
You can find the names of almost all fields, functions, keywords and operators
defined as constants in `JqlQuery` and static methods in `JqlFunciton` classes.
For more info see the Jira docs (link above).

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\JqlQuery;
use JiraRestApi\JiraException;
use JiraRestApi\Issue\JqlFunction;

try {
    $jql = new JqlQuery();

    $jql->setProject('TEST')
        ->setType('Bug')
        ->setStatus('In Progress')
        ->setAssignee(JqlFunction::currentUser())
        ->setCustomField('My Custom Field', 'value')
        ->addIsNotNullExpression('due');

    $issueService = new IssueService();

    $ret = $issueService->search($jql->getQuery());

    var_dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

#### Remote Issue Link


##### get remote issue link

* [See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-getRemoteIssueLinks)

```php

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\RemoteIssueLink;
use JiraRestApi\JiraException;

try {
    $issueService = new IssueService();

    $rils = $issueService->getRemoteIssueLink($issueKey);
        
    // rils is array of RemoteIssueLink classes
    var_dump($rils);
} catch (HTTPException $e) {
    $this->assertTrue(false, $e->getMessage());
}

```

##### create remote issue link

* [See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-getRemoteIssueLinks)

```php
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\RemoteIssueLink;
use JiraRestApi\JiraException;

$issueKey = 'TEST-316';

try {
	$issueService = new IssueService();

	$ril = new RemoteIssueLink();

	$ril->setUrl('http://www.mycompany.com/support?id=1')
		->setTitle('Remote Link Title')
		->setRelationship('causes')
		->setSummary('Crazy customer support issue')
	;

	$rils = $issueService->createOrUpdateRemoteIssueLink($issueKey, $ril);

    // rils is array of RemoteIssueLink classes
    var_dump($rils);
} catch (JiraException $e) {
	$this->assertTrue(false, 'Create Failed : '.$e->getMessage());
}
```

#### Issue time tracking

This methods use `get issue` and `edit issue` methods internally.

[See Jira API reference (get issue)](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-getIssue)

[See Jira API reference (edit issue)](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-editIssue)

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

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-addWorklog)

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

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-updateWorklog)

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

    $ret = $issueService->editWorklog($issueKey, $workLog, $workLogid);

    var_dump($ret);
} catch (JiraException $e) {
    $this->assertTrue(false, 'Edit worklog Failed : '.$e->getMessage());
}

```

#### Get issue worklog

[See Jira API reference (get full issue worklog)](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-getIssueWorklog)

[See Jira API reference (get worklog by id)](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-getWorklog)

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

#### Add watcher to Issue 

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-addWatcher)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$issueKey = 'TEST-961';

try {
    $issueService = new IssueService();
    
    // watcher's id
    $watcher = 'lesstif';
    
    $issueService->addWatcher($issueKey, $watcher);
    
} catch (JiraException $e) {
    $this->assertTrue(false, 'add watcher Failed : '.$e->getMessage());
}
```

#### issue notify

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issue-notify)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Notify;
use JiraRestApi\JiraException;

$issueKey = 'TEST-961';

try {
    $issueService = new IssueService();

    $noti = new Notify();

    $noti->setSubject('notify test')
        ->setTextBody('notify test text body')
        ->setHtmlBody('<h1>notify</h1>test html body')
        ->sendToAssignee(true)
        ->sendToWatchers(true)
        ->sendToUser('lesstif', true)
        ->sendToGroup('temp-group')
    ;

    $issueService->notify($issueKey, $noti);
    
} catch (JiraException $e) {
    $this->assertTrue(false, 'Issue notify Failed : '.$e->getMessage());
}
```
#### Create Issue Link

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issueLink-linkIssues)

The Link Issue Resource provides functionality to manage issue links.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;
use JiraRestApi\JiraException;

try {
    $il = new IssueLink();

    $il->setInwardIssue('TEST-258')
        ->setOutwardIssue('TEST-249')
        ->setLinkTypeName('Relates' )
        ->setComment('Linked related issue via REST API.');
            
    $ils = new IssueLinkService();

    $ret = $ils->addIssueLink($il);

} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

#### Get Issue LinkType

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/issueLinkType-getIssueLinkTypes)

Rest resource to retrieve a list of issue link types.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;
use JiraRestApi\JiraException;

try {
    $ils = new IssueLinkService();

    $ret = $ils->getIssueLinkTypes();
    
    var_dump($ret);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

#### Create User

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/user-createUser)

Create user. 
By default created user will not be notified with email. If password field is not set then password will be randomly generated.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;

try {
    $us = new UserService();

    // create new user
    $user = $us->create([
            'name'=>'charlie',
            'password' => 'abracadabra',
            'emailAddress' => 'charlie@atlassian.com',
            'displayName' => 'Charlie of Atlassian',
        ]);

    var_dump($user);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Get User Info

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/user-getUser)

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

#### Find Users

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/user-findUsers)

Returns a list of users that match the search string and/or property. 

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;

try {
    $us = new UserService();

    $paramArray = [
        'username' => '.', // get all users. 
        'startAt' => 0,
        'maxResults' => 1000,
        'includeInactive' => true,
        //'property' => '*',
        ];

    // get the user info.
    $users = $us->findUsers($paramArray);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Find Assignable Users

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/user-findAssignableUsers)

Returns a list of users that match the search string. 

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;

try {
    $us = new UserService();

    $paramArray = [
        //'username' => null,
        'project' => 'TEST',
        //'issueKey' => 'TEST-1',
        'startAt' => 0,
        'maxResults' => 50, //max 1000
        //'actionDescriptorId' => 1,
    ];

    $users = $us->findAssignableUsers($paramArray);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Create Group

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/group-createGroup)

Create new group.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\Group\GroupService;
use JiraRestApi\Group\Group;

try {
    $g = new Group();

    $g->name = 'Test group for REST API';

    $gs = new GroupService();

    $ret = $gs->createGroup($g);
	var_dump($ret);
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Get Users from group

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/group-getUsersFromGroup)

returns a paginated list of users who are members of the specified group and its subgroups.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\Group\GroupService;

try {
   $queryParam = [
        'groupname' => 'Test group for REST API',
        'includeInactiveUsers' => true, // default false
        'startAt' => 0,
        'maxResults' => 50,
    ];

    $gs = new GroupService();

    $ret = $gs->getMembers($queryParam);

    // print all users in the group
    foreach($ret->values as $user) {
        print_r($user);
    }
} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

### Add User to group

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/7.6.1/#api/2/group-addUserToGroup)

add user to given group.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\Group\GroupService;

try {
    $groupName  = '한글 그룹 name';
    $userName = 'lesstif';

    $gs = new GroupService();

    $ret = $gs->addUserToGroup($groupName, $userName);

    // print current state of the group.
    print_r($ret);

} catch (JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

# License

Apache V2 License

# JIRA Rest API Documents
* 6.4 - https://docs.atlassian.com/jira/REST/6.4/
* Jira Server latest - https://docs.atlassian.com/jira/REST/server/
* Jira Cloud latest - https://docs.atlassian.com/jira/REST/latest/
