<?php

namespace JiraRestApi\Status;

use JiraRestApi\JiraException;
use JsonMapper_Exception;

class StatusService extends \JiraRestApi\JiraClient
{
    private $uri = '/status';

    /**
     * get all statuses.
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     *
     * @return Status[] array of Project class
     */
    public function getAll()
    {
        $ret = $this->exec($this->uri.'/', null);
        $this->log->info("Result=\n".$ret);

        return $this->json_mapper->mapArray(
            json_decode($ret, false),
            new \ArrayObject(),
            \JiraRestApi\Status\Status::class
        );
    }
}
