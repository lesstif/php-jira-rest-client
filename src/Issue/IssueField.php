<?php declare(strict_types=1);

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

class IssueField implements \JsonSerializable
{
    use ClassSerialize;

    /** @var string */
    public $summary;

    /** @var array */
    public $progress;

    /** @var \JiraRestApi\Issue\TimeTracking */
    public $timeTracking;

    /** @var \JiraRestApi\Issue\IssueType|null */
    public $issuetype;

    /** @var Reporter|null */
    public $reporter;

    /** @var \DateTimeInterface */
    public $created;

    /** @var \DateTimeInterface */
    public $updated;

    /** @var string|null */
    public $description;

    /** @var \JiraRestApi\Issue\Priority|null */
    public $priority;

    /** @var \JiraRestApi\Issue\IssueStatus */
    public $status;

    /** @var array|null */
    public $labels;

    /** @var \JiraRestApi\Project\Project */
    public $project;

    /** @var string|null */
    public $environment;

    /** @var \JiraRestApi\Issue\Component[]|null */
    public $components;

    /** @var \JiraRestApi\Issue\Comments */
    public $comment;

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

    /** @var \JiraRestApi\Issue\Reporter|null */
    public $assignee;

    /** @var \JiraRestApi\Issue\Version[]|null */
    public $versions;

    /** @var \JiraRestApi\Issue\Attachment[]|null */
    public $attachment;

    /** @var string|null */
    public $aggregatetimespent;

    /** @var string|null */
    public $timeestimate;

    /** @var string|null */
    public $aggregatetimeoriginalestimate;

    /** @var string|null */
    public $resolutiondate;

    /** @var \DateTimeInterface|string|null */
    public $duedate;

    /** @var array */
    public $issuelinks;

    /** @var \JiraRestApi\Issue\Issue[] */
    public $subtasks;

    /** @var int */
    public $workratio;

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
        if ($updateIssue != true) {
            $this->project = new \JiraRestApi\Project\Project();

            $this->assignee = new Reporter();
            // priority maybe empty.
            //$this->priority = new Priority();

            $this->issuetype = new IssueType();
        }
    }

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

        return $vars;
    }

    /**
     * @return array|null
     */
    public function get_CustomFields()
    {
        return $this->customFields;
    }

    /**
     * @param string       $key
     * @param string|array $value
     *
     * @return $this
     */
    public function add_CustomField(string $key, $value)
    {
        $this->customFields[$key] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function get_ProjectKey(): string
    {
        return $this->project->key;
    }

    /**
     * @return string
     */
    public function get_ProjectId(): string
    {
        return $this->project->id;
    }

    /**
     * @param string|int $key
     *
     * @return IssueField
     */
    public function set_ProjectKey($key): IssueField
    {
        $this->project->key = $key;

        return $this;
    }

    /**
     * @param string|int $id
     *
     * @return IssueField
     */
    public function set_ProjectId($id): IssueField
    {
        $this->project->id = $id;

        return $this;
    }

    /**
     * @param string $summary
     *
     * @return IssueField
     */
    public function set_Summary(string $summary): IssueField
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * set issue reporter name.
     *
     * @param string $name
     *
     * @return IssueField
     */
    public function set_ReporterName(string $name): IssueField
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
     * @return IssueField
     */
    public function set_ReporterAccountId(string $accountId): IssueField
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
     *
     * @return IssueField
     */
    public function set_AssigneeName(string $name): IssueField
    {
        if (is_null($this->assignee)) {
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
     * @return IssueField
     */
    public function set_AssigneeAccountId(string $accountId): IssueField
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
     * @return IssueField
     */
    public function set_PriorityName(string $name): IssueField
    {
        if (is_null($this->priority)) {
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
     * @see \JiraRestApi\Issue\IssueFieldV3::add_DescriptionHeading
     * @see \JiraRestApi\Issue\IssueFieldV3::add_DescriptionParagraph
     *
     * @param string $description
     *
     * @return IssueField
     */
    public function set_Description(string $description): IssueField
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
     * @return IssueField
     */
    public function add_Version($version): IssueField
    {
        if (is_null($this->versions)) {
            $this->versions = [];
        }

        if (is_string($version)) {
            array_push($this->versions, new Version($version));
        } elseif (is_array($version)) {
            foreach ($version as $v) {
                array_push($this->versions, new Version($v));
            }
        }

        return $this;
    }

    /**
     * add issue label.
     *
     * @param string $label
     *
     * @return IssueField
     */
    public function add_Label(string $label): IssueField
    {
        if (is_null($this->labels)) {
            $this->labels = [];
        }

        array_push($this->labels, $label);

        return $this;
    }

    /**
     * set issue type.
     *
     * @param IssueType|string $issueType mixed IssueType or string
     *
     * @return IssueField
     */
    public function set_IssueType($issueType): IssueField
    {
        if (is_string($issueType)) {
            if (is_null($this->issuetype)) {
                $this->issuetype = new IssueType();
            }

            $this->issuetype->name = $issueType;
        } else {
            $this->issuetype = $issueType;
        }

        return $this;
    }

    /**
     * @return IssueType
     */
    public function get_IssueType(): IssueType
    {
        return $this->issuetype;
    }

    /**
     *  set parent issue.
     *
     * @param string|int $keyOrId
     */
    public function set_ParentKeyOrId($keyOrId): IssueField
    {
        if (is_numeric($keyOrId)) {
            $this->parent['id'] = $keyOrId;
        } elseif (is_string($keyOrId)) {
            $this->parent['key'] = $keyOrId;
        }

        return $this;
    }

    /**
     * @param Issue $parent
     *
     * @return IssueField
     */
    public function set_Parent(Issue $parent): IssueField
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * add issue component.
     *
     * @param string|array $component mixed string or array
     *
     * @return IssueField
     */
    public function add_Components($component): IssueField
    {
        if (is_null($this->components)) {
            $this->components = [];
        }

        if (is_string($component)) {
            array_push($this->components, new Component($component));
        } elseif (is_array($component)) {
            foreach ($component as $c) {
                array_push($this->components, new Component($c));
            }
        }

        return $this;
    }

    /**
     * set security level.
     *
     * @param int $id issue's security id
     *
     * @return IssueField
     */
    public function set_SecurityId($id): IssueField
    {
        if (empty($this->security)) {
            $this->security = new SecurityScheme();
        }

        $this->security->id = $id;

        return $this;
    }

    /**
     * @param \DateTimeInterface|string $duedate due date string or DateTimeInterface object
     * @param string                    $format  datetime string format.
     *
     * @return IssueField
     */
    public function set_DueDate($duedate, string $format = 'Y-m-d'): IssueField
    {
        if (is_string($duedate)) {
            $this->duedate = $duedate;
        } elseif ($duedate instanceof \DateTimeInterface) {
            $this->duedate = $duedate->format($format);
        } else {
            $this->duedate = null;
        }

        return $this;
    }

    /**
     * set Assignee to Unassigned.
     *
     * @see https://confluence.atlassian.com/jirakb/how-to-set-assignee-to-unassigned-via-rest-api-in-jira-744721880.html
     */
    public function set_AssigneeToUnassigned(): IssueField
    {
        if (is_null($this->assignee)) {
            $this->assignee = new Reporter();
        }

        $this->assignee->setWantUnassigned(true);

        return $this;
    }

    /**
     * @return IssueField
     */
    public function set_AssigneeToDefault(): IssueField
    {
        if (is_null($this->assignee)) {
            $this->assignee = new Reporter();
        }

        $this->assignee->name = '-1';

        return $this;
    }
}
