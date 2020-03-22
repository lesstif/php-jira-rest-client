<?php

namespace JiraRestApi\Group;

/**
 * Class to perform all groups related queries.
 */
class GroupService extends \JiraRestApi\JiraClient
{
    private $uri = '/group';

    /**
     * Function to get group.
     *
     * @param array $paramArray Possible values for $paramArray 'username', 'key'.
     *                          "Either the 'username' or the 'key' query parameters need to be provided".
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group|object
     */
    public function get($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        return $this->json_mapper->map(
            json_decode($ret),
            new Group()
        );
    }

    /**
     * Get users from group.
     *
     * @param array $paramArray groupname, includeInactiveUsers, startAt, maxResults
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return GroupSearchResult|object
     */
    public function getMembers($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.'/member'.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        $userData = json_decode($ret);

        $res = $this->json_mapper->map($userData, new GroupSearchResult());

        return $res;
    }

    /**
     * Creates a group by given group parameter.
     *
     * @param $group \JiraRestApi\Group\Group
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group|object
     */
    public function createGroup($group)
    {
        $data = json_encode($group);

        $ret = $this->exec($this->uri, $data);

        $this->log->info("Result=\n".$ret);

        $group = $this->json_mapper->map(
            json_decode($ret),
            new Group()
        );

        return $group;
    }

    /**
     * Adds given user to a group.
     *
     * @param string $groupName
     * @param string $userName
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group|object Returns the current state of the group.
     */
    public function addUserToGroup($groupName, $userName)
    {
        $data = json_encode(['name' => $userName]);

        $ret = $this->exec($this->uri.'/user?groupname='.urlencode($groupName), $data);

        $this->log->info("Result=\n".$ret);

        $group = $this->json_mapper->map(
            json_decode($ret),
            new Group()
        );

        return $group;
    }

    /**
     * Removes given user from a group.
     *
     * @param $groupName
     * @param $userName
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return null Returns no content
     */
    public function removeUserFromGroup($groupName, $userName)
    {
        $param = http_build_query(['groupname' => $groupName, 'username' => $userName]);

        $ret = $this->exec($this->uri.'/user?'.$param, [], 'DELETE');

        $this->log->info("Result=\n".$ret);

        return $ret;
    }
}
