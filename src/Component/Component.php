<?php

namespace JiraRestApi\Component;

use JiraRestApi\ClassSerialize;
use JiraRestApi\Project\Project;
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

    /**
     * uri which was hit.
     *
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var \JiraRestApi\User\User
     */
    public $lead;

    /**
     * @var string
     */
    public $assigneeType;

    /**
     * @var int
     */
    public $projectId;

    /**
     * @var string
     */
    public $project;

    /**
     * @var bool
     */
    public $isAssigneeTypeValid;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Component
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return Component
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $leadUserName
     *
     * @return Component
     */
    public function setLeadUserName($leadUserName)
    {
        if ($this->lead === null) {
            $this->lead = new User();
        }

        $this->lead->name = $leadUserName;

        return $this;
    }

    /**
     * @param string $assigneeType
     *
     * @return Component
     */
    public function setAssigneeType($assigneeType)
    {
        $this->assigneeType = $assigneeType;

        return $this;
    }

    /**
     * @param string $projectKey
     *
     * @return Component
     */
    public function setProjectKey($projectKey)
    {
        $this->project = $projectKey;

        return $this;
    }

    /**
     * @param Project $project
     *
     * @return $this
     */
    public function setProject(Project $project)
    {
        $this->project = $project->key;

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
