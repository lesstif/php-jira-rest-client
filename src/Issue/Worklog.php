<?php

namespace JiraRestApi\Issue;

/**
 * Class Worklog
 *
 * @package JiraRestApi\Issue
 */
class Worklog
{
    /**
     * @var int Start at position
     */
    protected $startAt;

    /**
     * @var int Maximum results
     */
    protected $maxResults;

    /**
     * @var int Total results
     */
    protected $total;

    /**
     * @var array Worklogs
     */
    protected $worklogs;

    /**
     * @return int
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @param int $startAt
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return int
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @param int $maxResults
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return array Worklogs
     */
    public function getWorklogs()
    {
        return $this->worklogs;
    }

    /**
     * @param array $worklogs Worklogs
     */
    public function setWorklogs($worklogs)
    {
        $this->worklogs = $worklogs;
    }

}
