<?php declare(strict_types=1);

namespace JiraRestApi\Priority;

use JiraRestApi\Exceptions\JiraException;
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
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Priority[] array of Priority class
     */
    public function getAll() :array
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
     * @param string|int $priorityId priority id
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return \JiraRestApi\Issue\Priority
     */
    public function get($priorityId) :Priority
    {
        $ret = $this->exec($this->uri."/$priorityId", null);

        $this->log->info("Result=\n".$ret);

        $priority = $this->json_mapper->map(json_decode($ret), new Priority());

        return $priority;
    }
}
