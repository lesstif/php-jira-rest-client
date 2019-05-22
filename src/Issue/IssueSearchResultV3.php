<?php

namespace JiraRestApi\Issue;

/**
 * Issue search result.
 */
class IssueSearchResultV3 extends IssueSearchResult
{
    /**
     * @var \JiraRestApi\Issue\IssueV3[]
     */
    public $issues;

    /**
     * @return \JiraRestApi\Issue\IssueV3[]
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @param \JiraRestApi\Issue\IssueV3[] $issues
     */
    public function setIssues($issues)
    {
        $this->issues = $issues;
    }
}
