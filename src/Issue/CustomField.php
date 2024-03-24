<?php

namespace JiraRestApi\Issue;

class CustomField implements \JsonSerializable
{
    public int $id;

    public string $name;

    public string $description;

    public array $type;

    public string $searcherKey;

    public array $projectIds;

    public array $issueTypeIds;

    public string $self;

    public int $numericId;

    public bool $isLocked;

    public bool $isManaged;

    public bool $isAllProjects;

    public bool $isTrusted;

    public int $projectsCount;

    public int $screensCount;

    public function jsonSerialize(): mixed
    {
        return array_filter(get_object_vars($this));
    }
}
