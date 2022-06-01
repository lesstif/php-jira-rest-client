<?php

namespace JiraRestApi\Issue;

use DateTimeInterface;
use JiraRestApi\ClassSerialize;
use JiraRestApi\Project\Project;

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

    public string $description;

    public ?Priority $priority = null;

    public ?IssueStatus $status = null;

    public array $labels;

    public Project $project;

    public ?string $environment;

    /* @var \JiraRestApi\Issue\Component[] This property must don't describe the type feature for JSON deserialized. */
    public $components;

    /** @var \JiraRestApi\Issue\Comments */
    public ?Comments $comment = null;

    /** @var object */
    public $votes;

    /** @var object|null */
    public $resolution;

    /** @var array */
    public $fixVersions;

    /** @var \JiraRestApi\Issue\Reporter|null */
    public $creator;

    /** @var object|null */
    public $watches;

    /** @var object|null */
    public $worklog;

    public ?Reporter $assignee = null;

    /** @var \JiraRestApi\Issue\Version[] */
    public $versions;

    /** @var \JiraRestApi\Issue\Attachment[] */
    public $attachment;

    /** @var string|null */
    public $aggregatetimespent;

    public ?string $timeestimate;

    public ?string $aggregatetimeoriginalestimate;

    public ?string $resolutiondate;

    public ?DateTimeInterface $duedate = null;

    private string $duedateString;

    public array $issuelinks;

    /** @var \JiraRestApi\Issue\Issue[] */
    public $subtasks;

    /** @var int */
    public int $workratio;

    /** @var object|null */
    public $aggregatetimeestimate;

    /** @var object|null */
    public $aggregateprogress;

    /** @var object|null */
    public $lastViewed;

    /** @var object|null */
    public $timeoriginalestimate;

    /** @var object|null */
    public $parent;

    /** @var array|null */
    public $customFields;

    /** @var \JiraRestApi\Issue\SecurityScheme|null */
    public $security;

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
    public function jsonSerialize()
    {
        $vars = array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });

        // if assignee property has empty value then remove it.
        // @see https://github.com/lesstif/php-jira-rest-client/issues/126
        // @see https://github.com/lesstif/php-jira-rest-client/issues/177
        if (!empty($this->assignee)) {
            // do nothing
            if ($this->assignee->isWantUnassigned() === true) {
            } elseif ($this->assignee->isEmpty()) {
                unset($vars['assignee']);
            }
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

    public function getCustomFields()
    {
        return $this->customFields;
    }

    public function addCustomField($key, $value)
    {
        $this->customFields[$key] = $value;

        return $this;
    }

    public function getProjectKey()
    {
        return $this->project->key;
    }

    public function getProjectId()
    {
        return $this->project->id;
    }

    public function setProjectKey($key)
    {
        $this->project->key = $key;

        return $this;
    }

    public function setProjectId($id)
    {
        $this->project->id = $id;

        return $this;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * set issue reporter name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setReporterName($name)
    {
        if (is_null($this->reporter)) {
            $this->reporter = new Reporter();
        }

        $this->reporter->name = $name;

        return $this;
    }

    /**
     * set issue reporter accountId.
     *
     * @param string $accountId
     *
     * @return $this
     */
    public function setReporterAccountId($accountId)
    {
        if (is_null($this->reporter)) {
            $this->reporter = new Reporter();
        }

        $this->reporter->accountId = $accountId;

        return $this;
    }

    /**
     * set issue assignee name.
     *
     * @param string $name
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
     *
     * @param string $accountId
     *
     * @return $this
     */
    public function setAssigneeAccountId(string $accountId)
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
     *
     * @param string $name
     *
     * @return $this
     */
    public function setPriorityNameAsString(string $name)
    {
        if ($this->priority === null) {
            $this->priority = new Priority();
        }

        $this->priority->name = $name;

        return $this;
    }

    /**
     * set issue description.
     *
     * REST API V3 must use addDescriptionXXXX
     *
     * @see \JiraRestApi\Issue\IssueFieldV3::addDescriptionHeading
     * @see \JiraRestApi\Issue\IssueFieldV3::addDescriptionParagraph
     *
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        if (!empty($description)) {
            $this->description = $description;
        }

        return $this;
    }

    /**
     * add a Affects version.
     *
     * @param string|array $version mixed string or array
     *
     * @return $this
     */
    public function addVersionAsString(string $version): static
    {
        if (is_null($this->versions)) {
            $this->versions = [];
        }

        array_push($this->versions, new Version($version));

        return $this;
    }

    public function addVersionAsArray(array $version): static
    {
        if (is_null($this->versions)) {
            $this->versions = [];
        }

        foreach ($version as $v) {
            array_push($this->versions, new Version($v));
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
     *
     * @param string $keyOrId
     */
    public function setParentKeyOrId($keyOrId)
    {
        if (is_numeric($keyOrId)) {
            $this->parent['id'] = $keyOrId;
        } elseif (is_string($keyOrId)) {
            $this->parent['key'] = $keyOrId;
        }

        return $this;
    }

    public function setParent(Issue $parent)
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
        \JiraRestApi\Dumper::dd($this->components);

        return $this;
    }

    public function addComponentAsString(string $component): static
    {
        $this->components[] = new Component($component);

        return $this;
    }

    /**
     * set security level.
     *
     * @param int $id issue's security id
     *
     * @return $this
     */
    public function setSecurityId($id)
    {
        if (empty($this->security)) {
            $this->security = new SecurityScheme();
        }

        $this->security->id = $id;

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
    public function setAssigneeToUnassigned()
    {
        if (is_null($this->assignee)) {
            $this->assignee = new Reporter();
        }

        $this->assignee->setWantUnassigned(true);

        return $this;
    }

    public function setAssigneeToDefault()
    {
        if (is_null($this->assignee)) {
            $this->assignee = new Reporter();
        }

        $this->assignee->name = '-1';

        return $this;
    }
}
