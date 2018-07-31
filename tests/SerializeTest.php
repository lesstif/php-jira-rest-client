<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\Reporter;

class SerializeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @see https://github.com/lesstif/php-jira-rest-client/issues/18
     */
    public function testDefaultAssignee()
    {
        $r = new Reporter();

        $r->name = '-1';
        $r->emailAddress = 'user@example.com';
        $r->avatarUrls = '';

        $d = $r->jsonSerialize();

        //  passing a name value of '' then serialized array has 'name' key and empty value.
       // $this->assertEquals(true, array_key_exists('name', $d), 'Can\'t found "name" key.');
        $this->assertEquals(false, array_key_exists('avatarUrls', $d));
    }

    public function testSerialize()
    {
        $r = new Reporter();

        $r->name = 'KwangSeob Jeong';
        $r->emailAddress = 'user@example.com';
        $r->avatarUrls = 'http://my.avatar.com/avatarUrls';
        $r->displayName = 'lesstif';

        $d = $r->toArray(['name', 'emailAddress'], $excludeMode = true);
        Dumper::dump($d);

        // serialized array have not 'name' and 'emailAddress' keys.
        $this->assertEquals(false, array_key_exists('name', $d), '"name" key is exists!.');
        $this->assertEquals(false, array_key_exists('emailAddress', $d));

        $d = $r->toArray(['name', 'emailAddress'], $excludeMode = false);

        // serialized array must have only 'name' and 'emailAddress' keys.
        $this->assertEquals(true, array_key_exists('name', $d), '"name" key is not exists!.');
        $this->assertEquals(true, array_key_exists('emailAddress', $d));
        $this->assertEquals(2, count($d));
        Dumper::dump($d);
    }
}
