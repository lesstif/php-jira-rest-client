<?php

namespace JiraRestApi\Issue;

class VersionUnresolvedCount implements \JsonSerializable
{
    /** @var string */
    public $self;

    /** @var int */
    public $issuesUnresolvedCount;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function setSelf($self)
    {
        $this->self = $self;

        return $this;
    }

    public function setIssuesUnresolvedCount($issuesUnresolvedCount)
    {
        $this->issuesUnresolvedCount = $issuesUnresolvedCount;

        return $this;
    }
}
