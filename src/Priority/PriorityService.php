<?php

namespace JiraRestApi\Priority;

use JiraRestApi\Issue\Priority;

/**
 * Class to query priority.
 */
class PriorityService extends \JiraRestApi\JiraClient
{
    private $uri = '/priority';

    /**
     * Function to get all priorities.
     *
     * @return array
     *@throws \JsonMapper_Exception
     *
     * @throws \JiraRestApi\JiraException
     */
    public function getAll()
    {
        $ret = $this->exec($this->uri, null);

        $this->log->info("Result=\n".$ret);

        $priorityData = json_decode($ret);
        $priorities = [];

        foreach ($priorityData as $priority) {
            $priorities[] = $this->json_mapper->map($priority, new Priority());
        }

        return $priorities;
    }

    /**
     *  get specific priority info.
     *
     * @param string $priorityId priority id
     *
     * @return \JiraRestApi\Issue\Priority
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function get(string $priorityId)
    {
        $ret = $this->exec($this->uri."/$priorityId", null);

        $this->log->info("Result=\n".$ret);

        $priority = $this->json_mapper->map(json_decode($ret), new Priority());

        return $priority;
    }
}
