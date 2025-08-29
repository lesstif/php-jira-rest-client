<?php

namespace JiraRestApi\Test;

use PHPUnit\Framework\TestCase;
use JiraRestApi\Dumper;
use JiraRestApi\JiraException;
use JiraRestApi\User\User;
use JiraRestApi\User\UserService;

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function create_user() : User
    {
        $user = null;
        try {
            $us = new UserService();

            $ar = [
                'name'=>'charlie',
                'password' => 'abracadabra',
                'emailAddress' => 'charlie@atlassian.com',
                'displayName' => 'Charlie of Atlassian',
            ];

            // create new user
            $user = $us->create($ar);

            $this->assertEquals($user->name, $ar['name']);
            $this->assertEquals($user->emailAddress, $ar['emailAddress']);

        } catch (JiraException $e) {
            $this->fail('testGetUser Failed : '.$e->getMessage());
        }

        return $user;
    }

    /**
     * @test
     * @depends create_user
     */
    public function get_user_info(User $user) : User
    {
        try {
            $us = new UserService();

            // get the user info.
            $user = $us->get(['username' => $user->name]);

            $this->assertNotNull($user);


        } catch (JiraException $e) {
            $this->fail('testGetUser Failed : '.$e->getMessage());
        }

        return $user;
    }

    /**
     * @test
     * @depends get_user_info
     */
    public function testAddUser(User $user) : User
    {
        $this->markTestSkipped('not yet implemented');
    }

    /**
     * @test
     * @depends get_user_info
     */
    public function search_user(User $user) : User
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

            $this->assertIsArray($users);

        } catch (JiraException $e) {
            $this->fail('testGetUser Failed : '.$e->getMessage());
        }

        return $user;
    }

    /**
     * @test
     * @depends search_user
     */
    public function search_assignable(User $user) : User
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

            $this->assertIsArray($users);
            $this->assertGreaterThan(1, count($users));

        } catch (JiraException $e) {
            $this->fail('testSearchAssignable Failed : '.$e->getMessage());
        }

        return $user;
    }
}
