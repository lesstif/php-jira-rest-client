<?php

namespace JiraRestApi\Issue;

class AgileIssueFields extends IssueFieldV3
{
    /** @var \JiraRestApi\Epic\Epic|null */
    public $epic;

    /** @var \JiraRestApi\Sprint\Sprint|null */
    public $sprint;

    /** @var \JiraRestApi\Sprint\Sprint[]|null */
    public $closedSprints;
}
