<?php

use JiraRestApi\Tests;

class SerializeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @see https://github.com/lesstif/php-jira-rest-client/issues/18
     */
    public function testEmptyAssignee()
    {
        $r = new \JiraRestApi\Issue\Reporter();

        $r->name = '';
        $r->emailAddress = 'user@example.com';
        $r->avatarUrls = '';

        $d = $r->jsonSerialize();

        //  passing a name value of '' then serialized array has 'name' key and empty value.
        $this->assertEquals(true, array_key_exists('name', $d), 'Can\'t found "name" key.');
        $this->assertEquals(false, array_key_exists('avatarUrls', $d));
    }
}
