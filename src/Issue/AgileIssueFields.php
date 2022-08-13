<?php

namespace JiraRestApi\Issue;

class AgileIssueFields extends IssueField
{
    /** @var \JiraRestApi\Epic\Epic|null */
    public $epic;

    /** @var \JiraRestApi\Sprint\Sprint|null */
    public $sprint;

    /** @var \JiraRestApi\Sprint\Sprint[]|null */
    public $closedSprints;
}
