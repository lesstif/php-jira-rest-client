<?php

use JiraRestApi\Dumper;
use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;

class UserTest extends PHPUnit_Framework_TestCase
{
    public function testGetUser()
    {
        try {
            $us = new UserService();

            // get the user info.
            $user = $us->get(['username' => 'lesstif']);

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

}
