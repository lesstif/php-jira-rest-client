<?php

namespace JiraRestApi\Issue;

class VersionIssueCounts implements \JsonSerializable
{
    /** @var string */
    public $self;

    /** @var int */
    public $issuesFixedCount;

    /** @var int */
    public $issuesAffectedCount;

    /** @var int */
    public $issueCountWithCustomFieldsShowingVersion;

    /** @var \JiraRestApi\Issue\CustomFieldUsage[] */
    public $customFieldUsage;

    public function __construct()
    {
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function setSelf($self)
    {
        $this->self = $self;

        return $this;
    }

    public function setIssuesFixedCount($issuesFixedCount)
    {
        $this->issuesFixedCount = $issuesFixedCount;

        return $this;
    }

    public function setIssuesAffectedCount($issuesAffectedCount)
    {
        $this->issuesAffectedCount = $issuesAffectedCount;

        return $this;
    }

    public function setIssueCountWithCustomFieldsShowingVersion($issueCountWithCustomFieldsShowingVersion)
    {
        $this->issueCountWithCustomFieldsShowingVersion = $issueCountWithCustomFieldsShowingVersion;

        return $this;
    }

    public function setCustomFieldUsage($customFieldUsage)
    {
        $this->customFieldUsage = $customFieldUsage;

        return $this;
    }
}
