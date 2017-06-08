<?php

use JiraRestApi\Dumper;
use JiraRestApi\Group\Group;
use JiraRestApi\Group\GroupService;
use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;
use JiraRestApi\JiraException;

class GroupTest extends PHPUnit_Framework_TestCase
{
    public function testCreateGroup()
    {
        $this->markTestSkipped();
        try {
            $g = new Group();

            $g->name = 'Test group for REST API';

            $gs = new GroupService();

            $ret = $gs->createGroup($g);

            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testCreateGroup Failed : '.$e->getMessage());
        }
    }

    public function testGetUsersFromGroup()
    {
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
            $this->assertTrue(false, 'testCreateGroup Failed : '.$e->getMessage());
        }
    }

    public function testAddUserToGroup()
    {
        try {
            $groupName  = '한글 그룹 name';
            $userName = 'lesstif';

            $gs = new GroupService();

            $ret = $gs->addUserToGroup($groupName, $userName);

            // print all users in the group
            print_r($ret);

        } catch (JiraException $e) {
            $this->assertTrue(false, 'testAddUserToGroup Failed : '.$e->getMessage());
        }
    }
}
