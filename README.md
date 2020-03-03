# PHP JIRA Rest Client

[![Latest Stable Version](https://poser.pugx.org/lesstif/php-jira-rest-client/v/stable)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Latest Unstable Version](https://poser.pugx.org/lesstif/php-jira-rest-client/v/unstable)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Build Status](https://travis-ci.org/lesstif/php-jira-rest-client.svg?branch=master)](https://travis-ci.org/lesstif/php-jira-rest-client)
[![StyleCI](https://styleci.io/repos/30015369/shield?branch=master&style=flat)](https://styleci.io/repos/30015369)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/lesstif/php-jira-rest-client/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lesstif/php-jira-rest-client/)
[![Coverage Status](https://coveralls.io/repos/github/lesstif/php-jira-rest-client/badge.svg?branch=master)](https://coveralls.io/github/lesstif/php-jira-rest-client?branch=master)
[![License](https://poser.pugx.org/lesstif/php-jira-rest-client/license)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Total Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/downloads)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Monthly Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/d/monthly)](https://packagist.org/packages/lesstif/php-jira-rest-client)
[![Daily Downloads](https://poser.pugx.org/lesstif/php-jira-rest-client/d/daily)](https://packagist.org/packages/lesstif/php-jira-rest-client)

# Requirements

- PHP >= 7.1
- [php JsonMapper](https://github.com/netresearch/jsonmapper)
- [phpdotenv](https://github.com/vlucas/phpdotenv)

# Installation

1. Download and Install PHP Composer.

   ``` sh
   curl -sS https://getcomposer.org/installer | php
   ```

2. Next, run the Composer command to install the latest version of php jira rest client.
   ``` sh
   php composer.phar require lesstif/php-jira-rest-client
   ```
    or add the following to your composer.json file.
   ```json
   {
       "require": {
           "lesstif/php-jira-rest-client": "^1.19"
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

**Laravel:** Once installed, if you are not using automatic package discovery, then you need to register the `JiraRestApi\JiraRestApiServiceProvider` service provider in your `config/app.php`.

# Configuration

you can choose loads environment variables either 'dotenv' or 'array'.

## use dotenv


copy .env.example file to .env on your project root.	

```sh
JIRA_HOST="https://your-jira.host.com"
JIRA_USER="jira-username"
JIRA_PASS="jira-password-OR-api-token"
# to enable session cookie authorization
# COOKIE_AUTH_ENABLED=true
# COOKIE_FILE=storage/jira-cookie.txt
# if you are behind a proxy, add proxy settings
PROXY_SERVER="your-proxy-server"
PROXY_PORT="proxy-port"
PROXY_USER="proxy-username"
PROXY_PASSWORD="proxy-password"
JIRA_REST_API_V3=false
```

**Important Note:**
As of March 15, 2018, in accordance to the [Atlassian REST API Policy](https://developer.atlassian.com/platform/marketplace/atlassian-rest-api-policy/), Basic auth with password to be deprecated.
Instead of password, you should using [API token](https://confluence.atlassian.com/cloud/api-tokens-938839638.html).

**Laravel Users:** 
If you are developing with laravel framework(5.x), you must append above configuration to your application .env file.

**REST API V3 Note:**
In accordance to the [Atlassian's deprecation notice](https://developer.atlassian.com/cloud/jira/platform/deprecation-notice-user-privacy-api-migration-guide/), After the 29th of april 2019, REST API no longer supported username and userKey, 
and instead use the account ID.
if you are JIRA Cloud users, you need to set *JIRA_REST_API_V3=true* in the .env file.

**CAUTION**
this library not fully supported JIRA REST API V3 yet. 

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
               'jiraPassword' => 'jira-password-OR-api-token',
               // to enable session cookie authorization (with basic authorization only)
               'cookieAuthEnabled' => true,
               'cookieFile' => storage_path('jira-cookie.txt'),
               // if you are behind a proxy, add proxy settings
               "proxyServer" => 'your-proxy-server',
               "proxyPort" => 'proxy-port',
               "proxyUser" => 'proxy-username',
               "proxyPassword" => 'proxy-password',
          )
   ));
```

# Usage

## Table of Contents

### Project
- [Create Project](#create-project)
- [Update Project](#update-project)
- [Delete Project](#delete-project)
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
    - [Update Labels](#update-labels)
    - [Update Fix Versions](#update-fix-versions)
- [Change assignee](#change-assignee)
- [Remove issue](#remove-issue)
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
- [Remove watcher from Issue](#remove-watcher-from-issue)
- [Send a notification to the recipients](#issue-notify)

### Comment
- [Add comment](#add-comment)
- [Get comment](#get-comment)
- [Delete comment](#delete-comment)
- [Update comment](#update-comment)

### IssueLink

* [Create Issue Link](#create-issue-link)
* [Get Issue LinkType](#get-issue-linktype)

### User
- [Create User](#create-user)
- [Get User Info](#get-user-info)
- [Find Users](#find-users)
- [Find Assignable Users](#find-assignable-users)
- [Find Users by query](#find-users-by-query)
- [Delete User](#delete-user)

### Group
- [Create Group](#create-group)
- [Get Users from group](#get-users-from-group)
- [Add User to group](#add-user-to-group)
- [Remove User from group](#remove-user-from-group)

### Priority
- [Get All Priority list](#get-all-priority-list)
- [Get Priority](#get-priority)

### Attachment
- [Get attachment Info](#get-attachment-info)
- [Remove attachment](#remove-attachment)

### Version
- [Create version](#create-version)
- [Update version](#update-version)
- [Delete version](#delete-version)
- [Get version related issues](#get-version-related-issues)
- [Get version unresolved issues](#get-version-related-issues)

### Component
- [Create component](#create-component)
- [Update component](#update-component)
- [Delete component](#delete-component)

### Board
- [Get board list](#get-board-list)
- [Get board info](#get-board-info)
- [Get board issues](#get-board-issues)
- [Get board epics](#get-board-epics)

### Epic
- [Get epic info](#)

#### Create Project

Create a new project.

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project-createProject)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\Project\Project;
use JiraRestApi\JiraException;

try {
    $p = new Project();

    $p->setKey('EX')
        ->setName('Example')
        ->setProjectTypeKey('business')
        ->setProjectTemplateKey('com.atlassian.jira-core-project-templates:jira-core-project-management')
        ->setDescription('Example Project description')
        ->setLead('lesstif')
        ->setUrl('http://example.com')
        ->setAssigneeType('PROJECT_LEAD')
        ->setAvatarId(10130)
        ->setIssueSecurityScheme(10000)
        ->setPermissionScheme(10100)
        ->setNotificationScheme(10100)
        ->setCategoryId(10100)
    ;

    $proj = new ProjectService();

    $pj = $proj->createProject($p);
   
    // "http://example.com/rest/api/2/project/10042"
    var_dump($pj->self);
    // 10042 
    var_dump($pj->id);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

#### Update Project

Update a project.
Only non null values sent in JSON will be updated in the project.

Values available for the assigneeType field are: "PROJECT_LEAD" and "UNASSIGNED".

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project-updateProject)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\Project\Project;
use JiraRestApi\JiraException;

try {
    $p = new Project();

    $p->setName('Updated Example')
        ->setProjectTypeKey('software')
        ->setProjectTemplateKey('com.atlassian.jira-software-project-templates:jira-software-project-management')
        ->setDescription('Updated Example Project description')
        ->setLead('new-leader')
        ->setUrl('http://new.example.com')
        ->setAssigneeType('UNASSIGNED')
    ;

    $proj = new ProjectService();

    $pj = $proj->updateProject($p, 'EX');
   
    var_dump($pj);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

#### Delete Project

Deletes a project.

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project-deleteProject)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $proj = new ProjectService();

    $pj = $proj->deleteProject('EX');
   
    var_dump($pj);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

#### Get Project Info

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project-getProject)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $proj = new ProjectService();

    $p = $proj->get('TEST');
	
    var_dump($p);			
} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

#### Get All Project list

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project-getAllProjects)

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
} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Get Project type

[See Jira API reference (get all types)](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project/type-getAllProjectTypes)

[See Jira API reference (get type)](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project/type-getProjectTypeByKey)

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

} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

#### Get Project Version

get all project's versions.

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project-getProjectVersions)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $proj = new ProjectService();

    $vers = $proj->getVersions('TEST');

    foreach ($vers as $v) {
        // $v is  JiraRestApi\Issue\Version
        var_dump($v);
    }
} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```

or get pagenated project's versions.

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/project-getProjectVersionsPaginated)

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
} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}

```


#### Get All Field List

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/field-getFields)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

#### Create Custom Field

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/field-createCustomField)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'Field Create Failed : '.$e->getMessage());
}
```

If you need a list of custom field types(ex. *com.atlassian.jira.plugin.system.customfieldtypes:grouppicker*) , check out [Get All Field list](#get-all-field-list).

#### Get Issue Info

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getIssue)

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
} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

You can access the custom field associated with issue through *$issue->fields->customFields* array or through direct custom field id variables(Ex: *$issue->fields->customfield_10300*).

#### Create Issue

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-createIssue)

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
                ->addComponents(['Component-1', 'Component-2'])
                // set issue security if you need.
                ->setSecurityId(10001 /* security scheme id */)
                ->setDueDate('2019-06-19')
            ;
	
    $issueService = new IssueService();

    $ret = $issueService->create($issueField);
	
    //If success, Returns a link to the created issue.
    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
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
                ->addCustomField('customfield_10100', 'text area body text') // String type custom field
                ->addCustomField('customfield_10200', ['value' => 'Linux']) // Select List (single choice)
                ->addCustomField('customfield_10408', [
                    ['value' => 'opt2'], ['value' => 'opt4']
                ]) // Select List (multiple choice)
    ;
	
    $issueService = new IssueService();

    $ret = $issueService->create($issueField);
	
    //If success, Returns a link to the created issue.
    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

Currently, not tested for all custom field types.

#### Create Multiple Issues

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-createIssues)

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
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

#### Create Sub Task

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-createIssue)

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
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

#### Create Issue using REST API V3

REST API V3' description field is complicated.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueFieldV3;
use JiraRestApi\JiraException;

try {
    $issueField = new IssueFieldV3();

    $paraDesc =<<< DESC

Full description for issue
- order list 1
- order list 2
-- sub order list 1
-- sub order list 1
- order list 3 
DESC;
    
    $issueField->setProjectKey("TEST")
                ->setSummary("something's wrong")
                ->setAssigneeAccountId("user-account-id-here")
                ->setPriorityName("Critical")
                ->setIssueType("Bug")
                ->addDescriptionHeading(3, 'level 3 heading here')
                ->addDescriptionParagraph($paraDesc)
                ->addVersion(["1.0.1", "1.0.3"])
                ->addComponents(['Component-1', 'Component-2'])
                // set issue security if you need.
                ->setDueDate('2019-06-19')
            ;
	
    $issueService = new IssueService();

    $ret = $issueService->create($issueField);
	
    //If success, Returns a link to the created issue.
    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
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
                ->addCustomField('customfield_10100', 'text area body text') // String type custom field
                ->addCustomField('customfield_10200', ['value' => 'Linux']) // Select List (single choice)
                ->addCustomField('customfield_10408', [
                    ['value' => 'opt2'], ['value' => 'opt4']
                ]) // Select List (multiple choice)
    ;
	
    $issueService = new IssueService();

    $ret = $issueService->create($issueField);
	
    //If success, Returns a link to the created issue.
    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

Currently, not tested for all custom field types.

#### Add Attachment

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue/%7BissueIdOrKey%7D/attachments-addAttachment)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(FALSE, "Attach Failed : " . $e->getMessage());
}

```

#### Update issue

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-editIssue)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(FALSE, "update Failed : " . $e->getMessage());
}
```

If you want to change the custom field type when updating an issue, you can call the *addCustomField* function just as you did for creating issue.


##### Update labels

This function is a convenient wrapper for add or remove label in the issue.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

try {
    $issueKey = 'TEST-123';

    $issueService = new IssueService();

    $addLabels = [
        'triaged', 'customer-request', 'sales-request'
    ];

    $removeLabel = [
        'will-be-remove', 'this-label-is-typo'
    ];

    $ret = $issueService->updateLabels($issueKey,
            $addLabels,
            $removeLabel,
            $notifyUsers = false
        );

    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'updateLabels Failed : '.$e->getMessage());
}
```

##### Update fix versions

This function is a convenient wrapper for add or remove fix version in the issue.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

try {
    $issueKey = 'TEST-123';

    $issueService = new IssueService();

    $addVersions = [
        '1.1.1', 'named-version'
    ];

    $removeVersions = [
        '1.1.0', 'old-version'
    ];

    $ret = $issueService->updateFixVersions($issueKey,
            $addVersions,
            $removeVersions,
            $notifyUsers = false
        );

    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'updateFixVersions Failed : '.$e->getMessage());
}
```

#### Change Assignee

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-assign)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(FALSE, "Change Assignee Failed : " . $e->getMessage());
}
```

REST API V3(JIRA Cloud) users must use *changeAssigneeByAccountId* method with accountId.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$issueKey = "TEST-879";

try {
	$issueService = new IssueService();

    $accountId = 'usre-account-id';

    $ret = $issueService->changeAssigneeByAccountId($issueKey, $accountId);

    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(FALSE, "Change Assignee Failed : " . $e->getMessage());
}
```   

#### Remove Issue

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-deleteIssue)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(FALSE, "Remove Issue Failed : " . $e->getMessage());
}
```

#### Add comment

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-addComment)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
}

```

#### Get comment

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getComments)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$issueKey = "TEST-879";

try {
    $issueService = new IssueService();

    $comments = $issueService->getComments($issueKey);

    var_dump($comments);

} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'get Comment Failed : '.$e->getMessage());
}

```

#### Delete comment

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-deleteComment)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$issueKey = "TEST-879";

try {
    $commentId = 12345;

    $issueService = new IssueService();

    $ret = $issueService->deleteComment($issueKey, $commentId);

} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'Delete comment Failed : '.$e->getMessage());
}

```

#### Update comment

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-updateComment)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use JiraRestApi\Issue\Comment;

$issueKey = "TEST-879";

try {
    $commentId = 12345;

    $issueService = new IssueService();
        
    $comment = new Comment();
    $comment->setBody('Updated comments');
    
    $issueService->updateComment($issueKey, $commentId, $comment);

} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'Update comment Failed : '.$e->getMessage());
}

```

#### Perform a transition on an issue

Note: this library uses goal **status names** instead of **transition names**.
So, if you want to change issue status to 'Some Status',
you should pass that status name to `setTransitionName`

i.e. `$transition->setTransitionName('Some Status')`

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-doTransition)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
}
```

#### Perform an advanced search

##### Simple Query

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/search-searchUsingSearchRequest)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

##### JQL with pagination

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/search-search)

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
        $ret = $issueService->search($jql, $startAt * $maxResult, $maxResult);

        print ("\nPaging $startAt\n");
        print ("-------------------\n");
        foreach ($ret->issues as $issue) {
            print (sprintf("%s %s \n", $issue->key, $issue->fields->summary));
        }
    }     
} catch (JiraRestApi\JiraException $e) {
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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}
```

#### Remote Issue Link


##### get remote issue link

* [See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getRemoteIssueLinks)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

$issueKey = 'TEST-316';

try {
    $issueService = new IssueService();

    $rils = $issueService->getRemoteIssueLink($issueKey);
        
    // rils is array of RemoteIssueLink classes
    var_dump($rils);
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, $e->getMessage());
}

```

##### create remote issue link

* [See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getRemoteIssueLinks)

```php
<?php
require 'vendor/autoload.php';

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
}
```

#### Issue time tracking

This methods use `get issue` and `edit issue` methods internally.

[See Jira API reference (get issue)](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getIssue)

[See Jira API reference (edit issue)](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-editIssue)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}

```

#### Add worklog in issue

[See Jira API V2 reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-addWorklog)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
}

```

[See Jira API V3 reference](https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-issue-issueIdOrKey-worklog-post)

```php
<?php
require 'vendor/autoload.php';

// Worklog example for API V3 assumes JIRA_REST_API_V3=true is configured in
// your .env file.

use JiraRestApi\Issue\ContentField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraException;

$issueKey = 'TEST-961';

try {
    $workLog = new Worklog();

    $paragraph = new ContentField();
    $paragraph->type = 'paragraph';
    $paragraph->content[] = [
        'text' => 'I did some work here.',
        'type' => 'text',
    ];

    $comment = new ContentField();
    $comment->type = 'doc';
    $comment->version = 1;
    $comment->content[] = $paragraph;

    $workLog->setComment($comment)
            ->setStarted('2016-05-28 12:35:54')
            ->setTimeSpent('1d 2h 3m');

    $issueService = new IssueService();

    $ret = $issueService->addWorklog($issueKey, $workLog);

    $workLogid = $ret->{'id'};

    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
}

```


#### edit worklog in issue

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-updateWorklog)

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
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'Edit worklog Failed : '.$e->getMessage());
}

```

#### Get issue worklog

[See Jira API reference (get full issue worklog)](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getIssueWorklog)

[See Jira API reference (get worklog by id)](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getWorklog)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
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
    
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
}

```

#### Add watcher to Issue 

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-addWatcher)

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
    
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'add watcher Failed : '.$e->getMessage());
}
```

#### Remove watcher from Issue

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-removeWatcher)

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
    
    $issueService->removeWatcher($issueKey, $watcher);
    
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'add watcher Failed : '.$e->getMessage());
}
```

#### issue notify

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-notify)

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
    
} catch (JiraRestApi\JiraException $e) {
    $this->assertTrue(false, 'Issue notify Failed : '.$e->getMessage());
}
```
#### Create Issue Link

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issueLink-linkIssues)

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

} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

#### Get Issue LinkType

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issueLinkType-getIssueLinkTypes)

Rest resource to retrieve a list of issue link types.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\IssueLink\IssueLinkService;
use JiraRestApi\JiraException;

try {
    $ils = new IssueLinkService();

    $ret = $ils->getIssueLinkTypes();
    
    var_dump($ret);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```

#### Create User

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/user-createUser)

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
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get User Info

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/user-getUser)

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
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Find Users

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/user-findUsers)

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
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Find Assignable Users

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/user-findAssignableUsers)

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
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```


#### Find users by query

[See Jira API reference](https://developer.atlassian.com/cloud/jira/platform/rest/v2/#api-rest-api-2-user-search-query-get)

Returns a list of users that match the search string.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;

try {
    $us = new UserService();

    $paramArray = [
      'query' => 'is watcher of TEST',
    ];

    $users = $us->findUsersByQuery($paramArray);
    var_dump($users);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### delete User

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/user-removeUser)

Removes user.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;

try {
    $us = new UserService();

    $paramArray = ['username' => 'user@example.com'];

    $users = $us->deleteUser($paramArray);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Create Group

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/group-createGroup)

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
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get Users from group

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/group-getUsersFromGroup)

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
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

### Add User to group

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/group-addUserToGroup)

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

} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

### Remove User from group

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/group-removeUserFromGroup)

Removes given user from a group.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\JiraException;
use JiraRestApi\Group\GroupService;

try {
    $groupName  = '한글 그룹 name';
    $userName = 'lesstif';

    $gs = new GroupService();

    $gs->removeUserFromGroup($groupName, $userName);

} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get All Priority list

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/priority)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Priority\PriorityService;
use JiraRestApi\JiraException;

try {
    $ps = new PriorityService();

    $p = $ps->getAll();
	
    var_dump($p);
} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

#### Get Priority

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/priority)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Priority\PriorityService;
use JiraRestApi\JiraException;

try {
    $ps = new PriorityService();

    $p = $ps->get(1);
	
    var_dump($p);
} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

#### Get Attachment Info

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/attachment-getAttachment)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Attachment\AttachmentService;
use JiraRestApi\JiraException;

try {
    $attachmentId = 12345;

    $atts = new AttachmentService();
    $att = $atts->get($attachmentId);

    var_dump($att);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```



Gets the attachment information and saves the attachment into the outDir directory.

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Attachment\AttachmentService;
use JiraRestApi\JiraException;

try {
    $attachmentId = 12345;
    $outDir = "attachment_dir";
    
    $atts = new AttachmentService();
    $att = $atts->get($attachmentId, $outDir, $overwrite = true);

    var_dump($att);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}
```


#### Remove attachment

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/attachment-removeAttachment)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Attachment\AttachmentService;
use JiraRestApi\JiraException;

try {
    $attachmentId = 12345;

    $atts = new AttachmentService();

    $atts->remove($attachmentId);
} catch (JiraRestApi\JiraException $e) {
	print("Error Occured! " . $e->getMessage());
}
```

#### Create version

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/version-createVersion)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Issue\Version;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\Version\VersionService;
use JiraRestApi\JiraException;

try {
    $projectService = new ProjectService();
    $project = $projectService->get('TEST');

    $versionService = new VersionService();

    $version = new Version();

    $version->setName('1.0.0')
            ->setDescription('Generated by script')
            ->setReleased(true)
            ->setReleaseDate(new \DateTime())
            ->setProjectId($project->id);

    $res = $versionService->create($version);

    var_dump($res);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Update version

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/version-updateVersion)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Version\VersionService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $versionService = new VersionService();
    $projectService = new ProjectService();

    $ver = $projectService->getVersion('TEST', '1.0.0');

    // update version
    $ver->setName($ver->name . ' Updated name')
        ->setDescription($ver->description . ' Updated description')
        ->setReleased(false)
        ->setReleaseDate(
            (new \DateTime())->add(date_interval_create_from_date_string('1 months 3 days'))
        );

    $res = $versionService->update($ver);

    var_dump($res);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Delete version

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/version-delete)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Version\VersionService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $versionService = new VersionService();
    $projectService = new ProjectService();

    $version = $projectService->getVersion('TEST', '1.0.0');

    $res = $versionService->delete($version);

    var_dump($res);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get version related issues

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/version-getVersionRelatedIssues)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Version\VersionService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $versionService = new VersionService();
    $projectService = new ProjectService();

    $version = $projectService->getVersion('TEST', '1.0.0');

    $res = $versionService->getRelatedIssues($version);

    var_dump($res);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get version unresolved issues

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/version-getVersionUnresolvedIssues)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Version\VersionService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

try {
    $versionService = new VersionService();
    $projectService = new ProjectService();

    $version = $projectService->getVersion('TEST', '1.0.0');

    $res = $versionService->getUnresolvedIssues($version);

    var_dump($res);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Create component

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/component-createComponent)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Component\ComponentService;
use JiraRestApi\Issue\Version;
use JiraRestApi\Project\Component;
use JiraRestApi\JiraException;

try {
    $componentService = new ComponentService();
    
    $component = new Component();
    $component->setName('my component')
              ->setDescription('Generated by script')
              ->setProjectKey('TEST');

    $res = $componentService->create($component);

    var_dump($res);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Update component

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/component-updateComponent)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Component\ComponentService;
use JiraRestApi\Issue\Version;
use JiraRestApi\Project\Component;
use JiraRestApi\JiraException;

try {
    $componentService = new ComponentService();
    
    $component = $componentService->get(10000); // component-id
    $component->setName($component->name . ' Updated name')
              ->setDescription($component->description . ' Updated descrption')
              ->setLeadUserName($component->lead->key);  // bug in jira api

    $res = $componentService->update($component);

    var_dump($res);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

##### Delete component

[See Jira API reference](https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/component-deleteComponent)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Component\ComponentService;
use JiraRestApi\Issue\Version;
use JiraRestApi\Project\Component;
use JiraRestApi\JiraException;

try {
    $componentService = new ComponentService();
    
    $component = $componentService->get(10000); // component-id

    $res = $componentService->delete($component);

    var_dump($res);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```


#### Get board list
[See Jira API reference](https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-board-get)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Board\BoardService;

try {
  $board_service = new BoardService();
  $board = $board_service->getBoardList();
  
  var_dump($board);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```
#### Get board info
[See Jira API reference](https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-board-boardId-get)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Board\BoardService;

try {
  $board_service = new BoardService();
  $board_id = 1;
  $board = $board_service->getBoard($board_id);
  
  var_dump($board);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get board issues
[See Jira API reference](https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-board-boardId-issue-get)

```php
<?php
require 'vendor/autoload.php';

use JiraRestApi\Board\BoardService;

try {
  $board_service = new BoardService();
  $board_id = 1;
  $issues = $board_service->getBoardIssues($board_id, [
    'maxResults' => 500,
    'jql' => urlencode('status != Closed'),
  ]);
  
  foreach ($issues as $issue) {
    var_dump($issue);
  }
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get board epics
[See Jira API reference](https://developer.atlassian.com/cloud/jira/software/rest/#api-agile-1-0-board-boardId-epic-get)

```php
<?php
require 'vendor/autoload.php';

try {
  $board_service = new JiraRestApi\Board\BoardService();
  $board_id = 1;
  $epics = $board_service->getBoardEpics($board_id, [
    'maxResults' => 500,
  ]);
  
  foreach ($epics as $epic) {
    var_dump($epic);
  }
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get epic info
[See Jira API reference](https://developer.atlassian.com/cloud/jira/software/rest/#api-agile-1-0-epic-epicIdOrKey-get)

```php
<?php
require 'vendor/autoload.php';

try {
  $epic_service = new JiraRestApi\Epic\EpicService();
  $epic_id = 1;
  $epic = $epic_service->getEpic($epic_id);
  
  var_dump($epic);
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

#### Get epic issues
[See Jira API reference](https://developer.atlassian.com/cloud/jira/software/rest/#api-agile-1-0-epic-epicIdOrKey-issue-get)

```php
<?php
require 'vendor/autoload.php';

try {
  $epic_service = new JiraRestApi\Epic\EpicService();
  $epic_id = 1;
  $issues = $epic_service->getEpicIssues($epic_id, [
    'maxResults' => 500,
    'jql' => urlencode('status != Closed'),
  ]);
  
  foreach ($issues as $issue) {
    var_dump($issue);
  }
} catch (JiraRestApi\JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}

```

# License

Apache V2 License

# JIRA Rest API Documents
* 6.4 - https://docs.atlassian.com/jira/REST/6.4/
* Jira Server latest - https://docs.atlassian.com/jira/REST/server/
* Jira Cloud latest - https://docs.atlassian.com/jira/REST/latest/
