<?php

namespace JiraRestApi\Project;

use JiraRestApi\ClassSerialize;
use JiraRestApi\Issue\IssueType;

class Project
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
     * @var Component[]
     */
    public $components;

    /**
     * IssueTypeList [\JiraRestApi\Issue\IssueType].
     *
     * @var IssueType[]
     */
    public $issueTypes;

    /** @var string */
    public $assigneeType;

    /** @var array */
    public $versions;

    /** @var array */
    public $roles;
}
