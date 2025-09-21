<?php

namespace JiraRestApi\Issue;

/**
 * Issue search result.
 */
class IssueBulkResult
{
    /**
     * @var string
     */
    public $expand;

    /**
     * @var \JiraRestApi\Issue\Issue[]
     */
    public $issues;

    /**
     * @var array
     */
    public $issueErrors;

    /**
     * @return array
     */
    public function getIssueErrors()
    {
        return $this->issueErrors;
    }

    /**
     * @param array $issueErrors
     */
    public function setIssueErrors($issueErrors)
    {
        $this->issueErrors = $issueErrors;
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
