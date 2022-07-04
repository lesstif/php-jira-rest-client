<?php

namespace JiraRestApi\Issue;

class IssueV3 extends Issue
{
    /** @var \JiraRestApi\Issue\IssueFieldV3 */
    public IssueField $fields;

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
