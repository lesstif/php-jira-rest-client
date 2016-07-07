<?php

namespace JiraRestApi\Project;

use JiraRestApi\ClassSerialize;

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

     /* @var string|null */
     public $description;

     /* Project leader info @var array */
     public $lead;

     /* @var ComponentList[\JiraRestApi\Project\Component] */
     public $components;

     /* @var IssueTypeList[\JiraRestApi\Issue\IssueType] */
     public $issueTypes;

     /* @var string */
     public $assigneeType;

     /* @var array */
     public $versions;

      /* @var array */
     public $roles;
}
