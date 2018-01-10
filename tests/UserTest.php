<?php

use JiraRestApi\Dumper;
use JiraRestApi\JiraException;
use JiraRestApi\User\User;
use JiraRestApi\User\UserService;

class UserTest extends PHPUnit_Framework_TestCase
{
    public function testCreateUser()
    {
        try {
            $us = new UserService();

            // create new user
            $user = $us->create([
                'name'=>'charlie',
                'password' => 'abracadabra',
                'emailAddress' => 'charlie@atlassian.com',
                'displayName' => 'Charlie of Atlassian',
            ]);

            Dumper::dump($user);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testGetUser Failed : '.$e->getMessage());
        }

        return $user;
    }

    /**
     * @depends testGetUser
     */
    public function testGetUser(User $user)
    {
        try {
            $us = new UserService();

            // get the user info.
            $user = $us->get(['username' => $user->username]);

            Dumper::dump($user);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testGetUser Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testGetUser
     */
    public function testAddUser()
    {
        $this->markTestSkipped('not yet implemented');
    }

    /**
     * @depends testGetUser
     */
    public function testSearch()
    {
        try {
            $us = new UserService();

            $paramArray = [
                'username'        => '.',  // . means all users
                'startAt'         => 0,
                'maxResults'      => 1000,
                'includeInactive' => true,
                //'property' => '*',
                ];

            // get the user info.
            $users = $us->findUsers($paramArray);

            Dumper::dump($users);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testGetUser Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testSearch
     */
    public function testSearchAssignable()
    {
        try {
            $us = new UserService();

            $paramArray = [
                //'username' => null,
                'project' => 'TEST',
                //'issueKey' => 'TEST-1',
                'startAt'    => 0,
                'maxResults' => 1000,
                //'actionDescriptorId' => 1,
            ];

            // get the user info.
            $users = $us->findAssignableUsers($paramArray);

            Dumper::dump($users);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearchAssignable Failed : '.$e->getMessage());
        }
    }
}
