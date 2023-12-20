<?php

namespace JiraRestApi\Component;

use JiraRestApi\AssigneeTypeEnum;
use JiraRestApi\ClassSerialize;
use JiraRestApi\User\User;

/**
 * Class Component.
 *
 *
 * @see https://docs.atlassian.com/jira/REST/server/#api/2/component
 */
class Component implements \JsonSerializable
{
    use ClassSerialize;

    /** uri which was hit.  */
    public string $self;

    public string $id;

    public string $name;

    public string $description;

    public ?User $lead;

    public string $leadUserName;

    public string $assigneeType;

    public int $projectId;

    public string $project;

    public bool $isAssigneeTypeValid;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription($description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setLeadUserName(string $leadUserName): static
    {
        $this->leadUserName = $leadUserName;

        return $this;
    }

    public function setAssigneeType(string $assigneeType): static
    {
        $this->assigneeType = $assigneeType;

        return $this;
    }

    public function setAssigneeTypeAsEnum(AssigneeTypeEnum $assigneeType): static
    {
        $this->assigneeType = $assigneeType->type();

        return $this;
    }

    public function setProjectKey(string $projectKey): static
    {
        $this->project = $projectKey;

        return $this;
    }

    public function setProject(string $project): static
    {
        $this->project = $project;

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });
    }
}
