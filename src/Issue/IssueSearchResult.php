<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 29.07.15
 * Time: 13:12.
 */

namespace JiraRestApi\Issue;

/**
 * Issue search result.
 */
class IssueSearchResult
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
     * @var \JiraRestApi\Issue\Issue[]
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
     * @return Issue[]
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @param Issue[] $issues
     */
    public function setIssues($issues)
    {
        $this->issues = $issues;
    }

    /**
     * @param int $ndx
     *
     * @return Issue
     */
    public function getIssue($ndx)
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
