<?php

namespace JiraRestApi\User;

/**
 * Class to perform all user related queries.
 *
 * @author Anik
 */
class UserService extends \JiraRestApi\JiraClient
{
    private $uri = '/user';

    /**
     * Function to get user.
     *
     * @param array $paramArray Possible values for $paramArray 'username', 'key'.
     *                          "Either the 'username' or the 'key' query parameters need to be provided".
     *
     * @return User class
     */
    public function get($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.$queryParam, null);

        $this->log->addInfo("Result=\n".$ret);

        return $this->json_mapper->map(
                json_decode($ret), new User()
        );
    }

    /**
     * Returns a list of users that match the search string and/or property.
     *
     * @param $paramArray
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return array
     */
    public function findUsers($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.'/search'.$queryParam, null);

        $this->log->addInfo("Result=\n".$ret);

        $userData = json_decode($ret);
        $users = [];

        foreach ($userData as $user) {
            $users[] = $this->json_mapper->map(
                $user, new User()
            );
        }

        return $users;
    }

    /**
     * Returns a list of users that match the search string.
     * Please note that this resource should be called with an issue key when a list of assignable users is retrieved for editing.
     *
     * @param $paramArray
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return array
     *
     * @see https://docs.atlassian.com/jira/REST/cloud/#api/2/user-findAssignableUsers
     */
    public function findAssignableUsers($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.'/assignable/search'.$queryParam, null);

        $this->log->addInfo("Result=\n".$ret);

        $userData = json_decode($ret);
        $users = [];

        foreach ($userData as $user) {
            $users[] = $this->json_mapper->map(
                $user, new User()
            );
        }

        return $users;
    }
}
