<?php declare(strict_types=1);

namespace JiraRestApi\Project;

use JiraRestApi\ClassSerialize;
use JiraRestApi\Exceptions\JiraException;

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
     * The account ID of the project lead.
     *
     * @var string
     */
    public $leadAccountId;

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

    public function jsonSerialize(): array
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
    public function set_Id(string $id): Project
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return Project
     */
    public function set_Key(string $key): Project
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Project
     */
    public function set_Name(string $name): Project
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param array $avatarUrls
     *
     * @return Project
     */
    public function set_AvatarUrls(array $avatarUrls): Project
    {
        $this->avatarUrls = $avatarUrls;

        return $this;
    }

    /**
     * @param array $projectCategory
     *
     * @return Project
     */
    public function set_ProjectCategory(array $projectCategory): Project
    {
        $this->projectCategory = $projectCategory;

        return $this;
    }

    /**
     * @param null|string $description
     *
     * @return Project
     */
    public function set_Description(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string|array $lead
     *
     * @return Project
     */
    public function set_Lead($lead): Project
    {
        if (is_string($lead)) {
            $this->lead = [$lead];
        } elseif (is_array($lead)) {
            $this->lead = $lead;
        }

        return $this;
    }

    /**
     * @param string $leadAccountId
     *
     * @return Project
     */
    public function set_LeadAccountId(string $leadAccountId): Project
    {
        $this->leadAccountId = $leadAccountId;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return Project
     */
    public function set_Url(string $url): Project
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string $projectTypeKey
     *
     * @return Project
     */
    public function set_ProjectTypeKey(string $projectTypeKey): Project
    {
        $this->projectTypeKey = $projectTypeKey;

        return $this;
    }

    /**
     * @param string $projectTemplateKey
     *
     * @return Project
     */
    public function set_ProjectTemplateKey(string $projectTemplateKey): Project
    {
        $this->projectTemplateKey = $projectTemplateKey;

        return $this;
    }

    /**
     * @param int $avatarId
     *
     * @return Project
     */
    public function set_AvatarId(int $avatarId): Project
    {
        $this->avatarId = $avatarId;

        return $this;
    }

    /**
     * @param int $issueSecurityScheme
     *
     * @return Project
     */
    public function set_IssueSecurityScheme(int $issueSecurityScheme): Project
    {
        $this->issueSecurityScheme = $issueSecurityScheme;

        return $this;
    }

    /**
     * @param int $permissionScheme
     *
     * @return Project
     */
    public function set_PermissionScheme(int $permissionScheme): Project
    {
        $this->permissionScheme = $permissionScheme;

        return $this;
    }

    /**
     * @param int $notificationScheme
     *
     * @return Project
     */
    public function set_NotificationScheme(int $notificationScheme): Project
    {
        $this->notificationScheme = $notificationScheme;

        return $this;
    }

    /**
     * @param int $categoryId
     *
     * @return Project
     */
    public function set_CategoryId(int $categoryId): Project
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
    public function set_AssigneeType(?string $assigneeType): Project
    {
        if (!in_array($assigneeType, ['PROJECT_LEAD', 'UNASSIGNED'])) {
            throw new JiraException('invalid assigneeType:'.$assigneeType);
        }

        $this->assigneeType = $assigneeType;

        return $this;
    }
}
