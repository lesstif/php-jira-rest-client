<?php

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

    /** @var \JiraRestApi\Issue\IssueType */
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

    /** @var array */
    public $labels;

    /** @var \JiraRestApi\Project\Project */
    public $project;

    /** @var string|null */
    public $environment;

    /** @var \JiraRestApi\Issue\Component[] */
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

    /** @var \JiraRestApi\Issue\Version[] */
    public $versions;

    /** @var \JiraRestApi\Issue\Attachment[] */
    public $attachment;

    /** @var string|null */
    public $aggregatetimespent;

    /** @var string|null */
    public $timeestimate;

    /** @var string|null */
    public $aggregatetimeoriginalestimate;

    /** @var string|null */
    public $resolutiondate;

    /** @var \DateTimeInterface|null */
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
     *
     * @return $this
     */
    public function setAssigneeName($name)
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
     * @return $this
     */
    public function setAssigneeAccountId($accountId)
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
    public function setPriorityName($name)
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
     * @param mixed string or array $name
     *
     * @return $this
     */
    public function addVersion($name)
    {
        if (is_null($this->versions)) {
            $this->versions = [];
        }

        if (is_string($name)) {
            array_push($this->versions, new Version($name));
        } elseif (is_array($name)) {
            foreach ($name as $v) {
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
     * @return $this
     */
    public function addLabel($label)
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
     * @param mixed IssueType or string $name
     *
     * @return $this
     */
    public function setIssueType($name)
    {
        if (is_string($name)) {
            if (is_null($this->issuetype)) {
                $this->issuetype = new IssueType();
            }

            $this->issuetype->name = $name;
        } else {
            $this->issuetype = $name;
        }

        return $this;
    }

    public function getIssueType()
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
     *
     * @param mixed string or array $component
     *
     * @return $this
     */
    public function addComponents($component)
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
     *
     * @param \DateTimeInterface|null $duedate due date string or DateTimeInterface object
     * @param string                  $format  datetime string format.
     *
     * @return $this
     */
    public function setDueDate($duedate, $format = 'Y-m-d')
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
