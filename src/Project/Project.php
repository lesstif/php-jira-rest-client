<?php

namespace JiraRestApi\Project;

use JiraRestApi\AssigneeTypeEnum;
use JiraRestApi\ClassSerialize;
use JiraRestApi\JiraException;

class Project implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * return only if Project query by key(not id).
     */
    public string $expand;

    /**
     * Project URI.
     */
    public string $self;

    /**
     * Project id.
     */
    public string $id;

    /**
     * Project key.
     */
    public ?string $key;

    /**
     * Project name.
     */
    public string $name;

    /**
     * avatar URL.
     */
    public \stdClass $avatarUrls;

    /**
     * Project category.
     */
    public \stdClass $projectCategory;

    public ?string $description = null;

    // Project leader info.
    public array $lead;

    private string $leadName;

    /**
     * The account ID of the project lead.
     *
     * @var string
     */
    public string $leadAccountId;

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

    public ?string $assigneeType;

    public ?array $versions = [];

    public ?array $roles;

    public string $url;

    public string $projectTypeKey;

    public ?string $projectTemplateKey;

    public int $avatarId;

    public int $issueSecurityScheme;

    public int $permissionScheme;

    public int $notificationScheme;

    public int $categoryId;

    public bool $simplified;

    public string $style;

    public bool $isPrivate;

    public array $properties;

    public bool $archived;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): mixed
    {
        $params = array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });
        if (!empty($this->leadName)) {
            $params['lead'] = $this->leadName;
            unset($params['leadName']);
        }
        if ($this->versions === null or count($this->versions) === 0) {
            unset($params['version']);
        }

        return $params;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setAvatarUrls(\stdClass $avatarUrls): static
    {
        $this->avatarUrls = $avatarUrls;

        return $this;
    }

    public function setProjectCategory(\stdClass $projectCategory): static
    {
        $this->projectCategory = $projectCategory;

        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setLeadName(string $leadName): static
    {
        $this->leadName = $leadName;

        return $this;
    }

    public function setLeadAccountId(string $leadAccountId): static
    {
        $this->leadAccountId = $leadAccountId;

        return $this;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function setProjectTypeKey(string $projectTypeKey): static
    {
        $this->projectTypeKey = $projectTypeKey;

        return $this;
    }

    public function setProjectTemplateKey(string $projectTemplateKey): static
    {
        $this->projectTemplateKey = $projectTemplateKey;

        return $this;
    }

    public function setAvatarId(int $avatarId): static
    {
        $this->avatarId = $avatarId;

        return $this;
    }

    public function setIssueSecurityScheme(int $issueSecurityScheme): static
    {
        $this->issueSecurityScheme = $issueSecurityScheme;

        return $this;
    }

    public function setPermissionScheme(int $permissionScheme): static
    {
        $this->permissionScheme = $permissionScheme;

        return $this;
    }

    public function setNotificationScheme(int $notificationScheme): static
    {
        $this->notificationScheme = $notificationScheme;

        return $this;
    }

    public function setCategoryId(int $categoryId): static
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * $assigneeType value available for "PROJECT_LEAD" and "UNASSIGNED".
     */
    public function setAssigneeType(?string $assigneeType): static
    {
        if (!in_array($assigneeType, ['PROJECT_LEAD', 'UNASSIGNED'])) {
            throw new JiraException('invalid assigneeType:'.$assigneeType);
        }

        $this->assigneeType = $assigneeType;

        return $this;
    }

    public function setAssigneeTypeAsEnum(AssigneeTypeEnum $assigneeType): static
    {
        $this->assigneeType = $assigneeType->type();

        return $this;
    }
}
