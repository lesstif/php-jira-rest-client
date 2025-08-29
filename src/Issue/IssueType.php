<?php

namespace JiraRestApi\Issue;

class IssueType implements \JsonSerializable
{
    public string $self;

    public string $id;

    public ?string $description;

    public string $iconUrl;

    public string $name;

    public bool $subtask;

    /** @var \JiraRestApi\Issue\IssueStatus[] */
    public $statuses;

    public int $avatarId;

    public int $hierarchyLevel;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
