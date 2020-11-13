<?php

declare(strict_types=1);

namespace JiraRestApi\Issue;

use JiraRestApi\JsonSerializableTrait;

class AgileIssue extends Issue
{
    use JsonSerializableTrait;

    /** @var \JiraRestApi\Issue\AgileIssueFields */
    public $fields;
}
