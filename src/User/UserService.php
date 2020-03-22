<?php

namespace JiraRestApi\User;

use JiraRestApi\Issue\Reporter;

/**
 * Class to perform all user related queries.
 *
 * @author Anik
 */
class UserService extends \JiraRestApi\JiraClient
{
    private $uri = '/user';

    /**
     * Function to create a new user.
     *
     * @param User|array $user
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return User|object User class
     */
    public function create($user)
    {
        $data = json_encode($user);

        $this->log->info("Create User=\n".$data);

        $ret = $this->exec($this->uri, $data, 'POST');

        return $this->json_mapper->map(
            json_decode($ret),
            new User()
        );
    }

    /**
     * Function to get user.
     *
     * @param array $paramArray Possible values for $paramArray 'username', 'key'.
     *                          "Either the 'username' or the 'key' query parameters need to be provided".
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return User|object User class
     */
    public function get($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        return $this->json_mapper->map(
            json_decode($ret),
            new User()
        );
    }

    /**
     * Returns a list of users that match the search string and/or property.
     *
     * @param array $paramArray
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return User[]
     */
    public function findUsers($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.'/search'.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        $userData = json_decode($ret);
        $users = [];

        foreach ($userData as $user) {
            $users[] = $this->json_mapper->map(
                $user,
                new User()
            );
        }

        return $users;
    }

    /**
     * Returns a list of users that match the search string.
     * Please note that this resource should be called with an issue key when a list of assignable users is retrieved for editing.
     *
     * @param array $paramArray
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return User[]
     *
     * @see https://docs.atlassian.com/jira/REST/cloud/#api/2/user-findAssignableUsers
     */
    public function findAssignableUsers($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.'/assignable/search'.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        $userData = json_decode($ret);
        $users = [];

        foreach ($userData as $user) {
            $users[] = $this->json_mapper->map(
                $user,
                new User()
            );
        }

        return $users;
    }

    /**
     * Returns a list of users that match with a specific query.
     *
     * @param array $paramArray
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return User[]
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v2/#api-rest-api-2-user-search-query-get
     */
    public function findUsersByQuery($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.'/search/query'.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        $userData = json_decode($ret);
        $users = [];

        foreach ($userData->values as $user) {
            $users[] = $this->json_mapper->map(
                $user,
                new User()
            );
        }

        return $users;
    }

    /**
     * Delete a User.
     *
     * @param $paramArray
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return bool
     */
    public function deleteUser($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.$queryParam, null, 'DELETE');

        return $ret;
    }

    /**
     * get a user info details.
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Reporter user Object
     */
    public function getMyself()
    {
        $ret = $this->exec('myself', null);

        $user = $this->json_mapper->map(
            json_decode($ret),
            new Reporter()
        );

        return $user;
    }

    /**
     * @param array $paramArray
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return User[]
     */
    public function getUsers($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec('/users'.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        $userData = json_decode($ret);
        $users = [];

        foreach ($userData as $user) {
            $users[] = $this->json_mapper->map($user, new User());
        }

        return $users;
    }
}
