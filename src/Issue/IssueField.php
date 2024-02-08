<?php

namespace JiraRestApi\Issue;

use AllowDynamicProperties;
use DateTimeInterface;
use JiraRestApi\ClassSerialize;
use JiraRestApi\Project\Project;

#[AllowDynamicProperties]
class IssueField implements \JsonSerializable
{
    use ClassSerialize;

    public string $summary;

    public array $progress;

    public ?TimeTracking $timeTracking = null;

    public ?IssueType $issuetype;

    public ?Reporter $reporter = null;

    public ?DateTimeInterface $created;

    public ?DateTimeInterface $updated = null;

    public ?string $description = null;

    public ?Priority $priority = null;

    public ?IssueStatus $status = null;

    public array $labels;

    public Project $project;

    public ?string $environment;

    /* @var \JiraRestApi\Issue\Component[] This property must don't describe the type feature for JSON deserialized. */
    public $components;

    public ?Comments $comment = null;

    public object $votes;

    public ?object $resolution;

    public array $fixVersions;

    public ?Reporter $creator;

    public ?object $watches;

    public ?object $worklog;

    public ?Reporter $assignee = null;

    /** @var \JiraRestApi\Issue\Version[] */
    public $versions;

    /** @var \JiraRestApi\Issue\Attachment[] */
    public $attachment;

    public ?string $aggregatetimespent;

    public ?string $timeestimate;

    public ?string $aggregatetimeoriginalestimate;

    public ?string $resolutiondate;

    public ?DateTimeInterface $duedate = null;

    private string $duedateString;

    public array $issuelinks;

    /** @var \JiraRestApi\Issue\Issue[] */
    public $subtasks;

    public int $workratio;

    public ?object $aggregatetimeestimate;

    public ?object $aggregateprogress;

    public ?object $lastViewed;

    public ?object $timeoriginalestimate;

    /** @var object|null */
    public $parent;

    public ?array $customFields;

    public ?SecurityScheme $security;

    public function __construct($updateIssue = false)
    {
        if ($updateIssue !== true) {
            $this->project = new \JiraRestApi\Project\Project();

            $this->assignee = new Reporter();
            // priority maybe empty.
            //$this->priority = new Priority();

            $this->issuetype = new IssueType();
        }
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): mixed
    {
        $vars = array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });

        // if assignee property has empty value then remove it.
        // @see https://github.com/lesstif/php-jira-rest-client/issues/126
        // @see https://github.com/lesstif/php-jira-rest-client/issues/177
        if (!empty($this->assignee) &&
            $this->assignee->isWantUnassigned() !== true &&
            $this->assignee->isEmpty()) {
            unset($vars['assignee']);
        }

        // clear undefined json property
        unset($vars['customFields']);

        // repackaging custom field
        if (!empty($this->customFields)) {
            foreach ($this->customFields as $key => $value) {
                $vars[$key] = $value;
            }
        }

        // replace duedate field
        if (!empty($this->duedateString)) {
            $vars['duedate'] = $this->duedateString;
            unset($vars['duedateString']);
        }

        return $vars;
    }

    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    public function addCustomField(string $key, null|string|int|float|array $value): static
    {
        $this->customFields[$key] = $value;

        return $this;
    }

    public function getProjectKey(): string
    {
        return $this->project->key;
    }

    public function getProjectId(): string
    {
        return $this->project->id;
    }

    public function setProjectKey(string $key): static
    {
        $this->project->key = $key;

        return $this;
    }

    public function setProjectId(string $id): static
    {
        $this->project->id = $id;

        return $this;
    }

    public function setSummary(string $summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * set issue reporter name.
     */
    public function setReporterName(string $name): static
    {
        if (is_null($this->reporter)) {
            $this->reporter = new Reporter();
        }

        $this->reporter->name = $name;

        return $this;
    }

    /**
     * set issue reporter accountId.
     */
    public function setReporterAccountId(string $accountId): static
    {
        if (is_null($this->reporter)) {
            $this->reporter = new Reporter();
        }

        $this->reporter->accountId = $accountId;

        return $this;
    }

    /**
     * set issue assignee name.
     */
    public function setAssigneeNameAsString(string $name): static
    {
        if ($this->assignee === null) {
            $this->assignee = new Reporter();
        }

        $this->assignee->name = $name;

        return $this;
    }

    /**
     * set issue assignee accountId.
     */
    public function setAssigneeAccountId(string $accountId): static
    {
        if (is_null($this->assignee)) {
            $this->assignee = new Reporter();
        }

        $this->assignee->accountId = $accountId;

        // REST API V3 must name field set to null.
        $this->assignee->name = null;
        $this->assignee->setWantUnassigned(true);

        return $this;
    }

    /**
     * set issue priority name.
     */
    public function setPriorityNameAsString(string $name): static
    {
        if ($this->priority === null) {
            $this->priority = new Priority();
        }

        $this->priority->name = $name;

        return $this;
    }

    /**
     * set issue description.
     */
    public function setDescription(?string $description): static
    {
        if (!empty($description)) {
            $this->description = $description;
        }

        return $this;
    }

    /**
     * add a Affects version.
     */
    public function addVersionAsString(string $version): static
    {
        if (is_null($this->versions)) {
            $this->versions = [];
        }

        $this->versions[] = new Version($version);

        return $this;
    }

    public function addVersionAsArray(array $version): static
    {
        if (is_null($this->versions)) {
            $this->versions = [];
        }

        foreach ($version as $v) {
            $this->versions[] = new Version($v);
        }

        return $this;
    }

    /**
     * add issue label.
     */
    public function addLabelAsString(string $label): static
    {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * set issue type.
     *
     * @param string $issueTypeName IssueType as string(for example,Bug, Task, etc..)
     */
    public function setIssueTypeAsString(string $issueTypeName): static
    {
        $this->issuetype = new IssueType();

        $this->issuetype->name = $issueTypeName;

        return $this;
    }

    public function setIssueType(IssueType $issueType): static
    {
        $this->issuetype = $issueType;

        return $this;
    }

    public function getIssueType(): IssueType
    {
        return $this->issuetype;
    }

    /**
     *  set parent issue.
     */
    public function setParentKeyOrId(string $keyOrId): static
    {
        if (is_null($this->parent)) {
            $this->parent = new Issue();
        }

        if (is_numeric($keyOrId)) {
            $this->parent->id = $keyOrId;
        } else {
            $this->parent->key = $keyOrId;
        }

        return $this;
    }

    public function setParent(?Issue $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * add issue component.
     */
    public function addComponentsAsArray(array $component): static
    {
        foreach ($component as $c) {
            $this->components[] = new Component($c);
        }

        return $this;
    }

    public function addComponentAsString(string $component): static
    {
        $this->components[] = new Component($component);

        return $this;
    }

    /**
     * set security level.
     */
    public function setSecurityId(int $issue_security_id): static
    {
        if (empty($this->security)) {
            $this->security = new SecurityScheme();
        }

        $this->security->id = $issue_security_id;

        return $this;
    }

    /**
     * set issue's due date.
     */
    public function setDueDateAsString(string $duedate): static
    {
        $this->duedateString = $duedate;

        return $this;
    }

    public function setDueDateAsDateTime(DateTimeInterface $duedate, $format = 'Y-m-d'): static
    {
        $this->duedateString = $duedate->format($format);

        return $this;
    }

    /**
     * set Assignee to Unassigned.
     *
     * @see https://confluence.atlassian.com/jirakb/how-to-set-assignee-to-unassigned-via-rest-api-in-jira-744721880.html
     */
    public function setAssigneeToUnassigned(): static
    {
        if (is_null($this->assignee)) {
            $this->assignee = new Reporter();
        }

        $this->assignee->setWantUnassigned(true);

        return $this;
    }

    public function setAssigneeToDefault(): static
    {
        if (is_null($this->assignee)) {
            $this->assignee = new Reporter();
        }

        $this->assignee->name = '-1';

        return $this;
    }
}
