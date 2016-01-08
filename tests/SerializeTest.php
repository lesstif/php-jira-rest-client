<?php

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\Transition;

class SerializeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @see https://github.com/lesstif/php-jira-rest-client/issues/18
     */
    public function testEmptyAssignee()
    {
        $r = new Reporter();

        $r->name = '';
        $r->emailAddress = 'user@example.com';
        $r->avatarUrls = '';

        $d = $r->jsonSerialize();

        //  passing a name value of '' then serialized array has 'name' key and empty value.
        $this->assertEquals(true, array_key_exists('name', $d), 'Can\'t found "name" key.');
        $this->assertEquals(false, array_key_exists('avatarUrls', $d));
    }
}