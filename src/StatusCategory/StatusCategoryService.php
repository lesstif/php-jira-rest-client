<?php

namespace JiraRestApi\StatusCategory;

use JiraRestApi\JiraException;
use JsonMapper_Exception;

class StatusCategoryService extends \JiraRestApi\JiraClient
{
    private $uri = '/statuscategory';

    /**
     * get all statuscategorys.
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     *
     * @return Statuscategory[] array of Project class
     */
    public function getAll()
    {
        $ret = $this->exec($this->uri.'/', null);
        $this->log->info("Result=\n".$ret);

        return $this->json_mapper->mapArray(
            json_decode($ret, false),
            new \ArrayObject(),
            \JiraRestApi\Issue\Statuscategory::class
        );
    }
}
