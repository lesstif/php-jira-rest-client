<?php

namespace JiraRestApi\Project;

use JiraRestApi\ClassSerialize;
use JiraRestApi\JiraException;

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

    /** @var string */
    public $url;

    /** @var string */
    public $projectTypeKey;

    /** @var string */
    public $projectTemplateKey;

    /** @var int */
    public $avatarId;

    /** @var int */
    public $issueSecurityScheme;

    /** @var int */
    public $permissionScheme;

    /** @var int */
    public $notificationScheme;

    /** @var int */
    public $categoryId;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });
    }

    /**
     * @param string $id
     *
     * @return Project
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return Project
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param array $avatarUrls
     *
     * @return Project
     */
    public function setAvatarUrls($avatarUrls)
    {
        $this->avatarUrls = $avatarUrls;

        return $this;
    }

    /**
     * @param array $projectCategory
     *
     * @return Project
     */
    public function setProjectCategory($projectCategory)
    {
        $this->projectCategory = $projectCategory;

        return $this;
    }

    /**
     * @param null|string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param array $lead
     *
     * @return Project
     */
    public function setLead($lead)
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return Project
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string $projectTypeKey
     *
     * @return Project
     */
    public function setProjectTypeKey($projectTypeKey)
    {
        $this->projectTypeKey = $projectTypeKey;

        return $this;
    }

    /**
     * @param string $projectTemplateKey
     *
     * @return Project
     */
    public function setProjectTemplateKey($projectTemplateKey)
    {
        $this->projectTemplateKey = $projectTemplateKey;

        return $this;
    }

    /**
     * @param int $avatarId
     *
     * @return Project
     */
    public function setAvatarId($avatarId)
    {
        $this->avatarId = $avatarId;

        return $this;
    }

    /**
     * @param int $issueSecurityScheme
     *
     * @return Project
     */
    public function setIssueSecurityScheme($issueSecurityScheme)
    {
        $this->issueSecurityScheme = $issueSecurityScheme;

        return $this;
    }

    /**
     * @param int $permissionScheme
     *
     * @return Project
     */
    public function setPermissionScheme($permissionScheme)
    {
        $this->permissionScheme = $permissionScheme;

        return $this;
    }

    /**
     * @param int $notificationScheme
     *
     * @return Project
     */
    public function setNotificationScheme($notificationScheme)
    {
        $this->notificationScheme = $notificationScheme;

        return $this;
    }

    /**
     * @param int $categoryId
     *
     * @return Project
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @param null|string $assigneeType value available for "PROJECT_LEAD" and "UNASSIGNED".
     *
     * @throws JiraException
     *
     * @return Project
     */
    public function setAssigneeType($assigneeType)
    {
        if (!in_array($assigneeType, ['PROJECT_LEAD', 'UNASSIGNED'])) {
            throw new JiraException('invalid assigneeType:'.$assigneeType);
        }

        $this->assigneeType = $assigneeType;

        return $this;
    }
}
