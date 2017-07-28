<?php

namespace JiraRestApi\Group;

/**
 * Class to perform all groups related queries.
 * @package JiraRestApi\Group
 */
class GroupService extends \JiraRestApi\JiraClient
{
    private $uri = '/group';

    /**
     * Function to get group.
     *
     * @param array $paramArray Possible values for $paramArray 'username', 'key'.
     *   "Either the 'username' or the 'key' query parameters need to be provided".
     *
     * @return Group class
     */
    public function get($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.$queryParam, null);

        $this->log->addInfo("Result=\n".$ret);

        return $this->json_mapper->map(
                json_decode($ret), new Group()
        );
    }

    /**
     * Get users from group
     *
     * @param $paramArray groupname, includeInactiveUsers, startAt, maxResults
     * @return GroupSearchResult
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function getMembers($paramArray)
    {
        $queryParam = '?' . http_build_query($paramArray);

        $ret = $this->exec($this->uri . '/member'.$queryParam, null);

        $this->log->addInfo("Result=\n".$ret);

        $userData = json_decode($ret);

        $res = $this->json_mapper->map($userData, new GroupSearchResult());

        return $res;
    }

    /**
     * Creates a group by given group parameter
     *
     * @param $group \JiraRestApi\Group\Group
     * @return array
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function createGroup($group)
    {
        $data = json_encode($group);

        $ret = $this->exec($this->uri, $data);

        $this->log->addInfo("Result=\n".$ret);

        $groupData = json_decode($ret);
        $groups = [];

        $group = $this->json_mapper->map(
            json_decode($ret), new Group()
        );

        return $group;
    }

    /**
     * Adds given user to a group.
     *
     * @param $group
     * @return Returns the current state of the group.
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function addUserToGroup($groupName, $userName)
    {
        $data = json_encode(['name' => $userName,]);

        $ret = $this->exec($this->uri . '/user?groupname=' . urlencode($groupName), $data);

        $this->log->addInfo("Result=\n".$ret);

        $group = $this->json_mapper->map(
            json_decode($ret), new Group()
        );

        return $group;
    }

    /**
     * Removes given user from a group.
     *
     * @param $groupName
     * @param $userName
     * @return null Returns no content
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function removeUserFromGroup($groupName, $userName)
    {
        $param = http_build_query(['groupname' => $groupName, 'username' => $userName]);

        $ret = $this->exec($this->uri . '/user?' . $param, [], 'DELETE');

        $this->log->addInfo("Result=\n".$ret);

        return $ret;
    }
}
