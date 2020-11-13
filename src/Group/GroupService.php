<?php

declare(strict_types=1);

namespace JiraRestApi\Group;

use JiraRestApi\Exceptions\JiraException;

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
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group
     */
    public function get(array $paramArray): Group
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
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return GroupSearchResult
     */
    public function getMembers(array $paramArray): GroupSearchResult
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
     * @param \JiraRestApi\Group\Group $group
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group
     */
    public function createGroup(Group $group): Group
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
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group Returns the current state of the group.
     */
    public function addUserToGroup(string $groupName, string $userName): Group
    {
        $data = json_encode([
            'name'      => $userName,
            'accountId' => $userName,
        ]);

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
     * @param string $groupName
     * @param string $userName
     *
     * @throws JiraException
     *
     * @return string|null Returns no content
     */
    public function removeUserFromGroup(string $groupName, string $userName)
    {
        $param = http_build_query(['groupname' => $groupName, 'username' => $userName]);

        $ret = $this->exec($this->uri.'/user?'.$param, null, 'DELETE');

        $this->log->info("Result=\n".$ret);

        return $ret;
    }
}
