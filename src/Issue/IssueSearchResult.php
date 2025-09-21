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
    public ?string $nextPageToken = null;

    public ?string $expand = null;

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
    public function getNextPageToken()
    {
        return $this->nextPageToken;
    }

    /**
     * @param string $nextPageToken
     */
    public function setNextPageToken($nextPageToken)
    {
        $this->nextPageToken = $nextPageToken;
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
     * @return ?string
     */
    public function getExpand(): ?string
    {
        return $this->expand;
    }

    public function setExpand(?string $expand)
    {
        $this->expand = $expand;
    }
}
