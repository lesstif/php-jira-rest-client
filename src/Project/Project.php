<?php

namespace JiraRestApi\Project;

use JiraRestApi\ClassSerialize;

class Project implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * return only if Project query by key(not id).
     *
     * @var string
     */
    public $expand;

    /**
     * Project URI.
     *
     * @var string
     */
    public $self;

    /**
     * Project id.
     *
     * @var string
     */
    public $id;

    /**
     * Project key.
     *
     * @var string
     */
    public $key;

    /**
     * Project name.
     *
     * @var string
     */
    public $name;

    /**
     * avatar URL.
     *
     * @var array
     */
    public $avatarUrls;

    /**
     * Project category.
     *
     * @var array
     */
    public $projectCategory;

    /** @var string|null */
    public $description;

    /**
     * Project leader info.
     *
     * @var array
     */
    public $lead;

    /**
     * ComponentList [\JiraRestApi\Project\Component].
     *
     * @var \JiraRestApi\Project\Component[]
     */
    public $components;

    /**
     * IssueTypeList [\JiraRestApi\Issue\IssueType].
     *
     * @var \JiraRestApi\Issue\IssueType[]
     */
    public $issueTypes;

    /** @var string|null */
    public $assigneeType;

    /** @var array|null */
    public $versions;

    /** @var array|null */
    public $roles;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });
    }
}
