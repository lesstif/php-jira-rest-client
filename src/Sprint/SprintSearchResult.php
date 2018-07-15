<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 29.07.15
 * Time: 13:12.
 */

namespace JiraRestApi\Sprint;

/**
 * Sprint search result.
 */
class SprintSearchResult
{
    /**
     * @var string
     */
    public $expand;

    /**
     * @var int
     */
    public $startAt;

    /**
     * @var int
     */
    public $maxResults;

    /**
     * @var int
     */
    public $total;

    /**
     * @var Sprint[]
     */
    public $issues;

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
     * @return Sprint[]
     */
    public function getSprints()
    {
        return $this->issues;
    }

    /**
     * @param Sprint[] $issues
     */
    public function setSprints($issues)
    {
        $this->issues = $issues;
    }

    /**
     * @param int $ndx
     *
     * @return object
     */
    public function getSprint($ndx)
    {
        return $this->issues[$ndx];
    }

    /**
     * @return string
     */
    public function getExpand()
    {
        return $this->expand;
    }

    /**
     * @param string $expand
     */
    public function setExpand($expand)
    {
        $this->expand = $expand;
    }
}
