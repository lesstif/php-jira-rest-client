<?php

namespace JiraRestApi\Priority;

/**
 * Class to query priority.
 */
class PriorityService extends \JiraRestApi\JiraClient
{
    private $uri = '/priority';

    /**
     * Function to get all priorities.
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Priority|object Priority class
     */
    public function getAll()
    {
        $queryParam = '?'.http_build_query();

        $ret = $this->exec($this->uri, null);

        $this->log->addInfo("Result=\n".$ret);

        $priorityData = json_decode($ret);
        $priorities = [];

        foreach ($priorityData as $priority) {
            $priorities[] = $this->json_mapper->map($priority, new Priority());
        }

        return $priorities;
    }
}
