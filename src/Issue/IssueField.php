<?php

namespace JiraRestApi\Issue;

use JiraRestApi\Project\Project;

class IssueField implements \JsonSerializable
{
    /**
     * @var string
     */
    public $summary;

    /**
     * @var array
     */
    public $progress;

    /**
     * @var \JiraRestApi\Issue\TimeTracking
     */
    public $timeTracking;

    /**
     * @var \JiraRestApi\Issue\IssueType
     */
    public $issuetype;

    /**
     * @var string
     */
    public $timespent;

    /**
     * @var \JiraRestApi\Issue\Reporter
     */
    public $reporter;

    /**
     * @var \DateTime
     */
    public $created;

//    /**
//     * @var \DateTime
//     */
//    public $updated;

    /**
     * @var string
     */
    public $description;

    /**
     * @var \JiraRestApi\Issue\Priority
     */
    public $priority;

    /**
     * @var object
     */
    public $status;

    /**
     * @var array
     */
    public $labels;

    /**
     * @var \JiraRestApi\Project\Project
     */
    public $project;

    /**
     * @var string
     */
    public $environment;

    /**
     * @var array
     */
    public $components;

    /**
     * @var \JiraRestApi\Issue\Comments
     */
    public $comment;

    /**
     * @var object
     */
    public $votes;

    /**
     * @var object
     */
    public $resolution;

    /**
     * @var array
     */
    public $fixVersions;

    /**
     * @var \JiraRestApi\Issue\Reporter
     */
    public $creator;

    /**
     * @var object
     */
    public $watches;

    /**
     * @var object
     */
    public $worklog;

    /**
     * @var \JiraRestApi\Issue\Reporter
     */
    public $assignee;

    /**
     * @var \JiraRestApi\Issue\Version[]
     */
    public $versions;

    /**
     * @var \JiraRestApi\Issue\Attachment[]
     */
    public $attachment;

    /**
     * @var string|null
     */
    public $aggregatetimespent;

    /**
     * @var string
     */
    public $timeestimate;

    /**
     * @var string
     */
    public $aggregatetimeoriginalestimate;

    /**
     * @var string
     */
    public $resolutiondate;

    /**
     * @var \DateTime
     */
    public $duedate;

    /**
     * @var array
     */
    public $issuelinks;

    /**
     * @var array
     */
    public $subtasks;

    /**
     * @var int|null
     */
    public $workratio;

    /**
     * @var object|null
     */
    public $aggregatetimeestimate;

    /**
     * @var object|null
     */
    public $aggregateprogress;

    /**
     * @var object|null
     */
    public $lastViewed;

    /**
     * @var object|null
     */
    public $timeoriginalestimate;

    /**
     * IssueField constructor.
     * @param bool $updateIssue
     */
    public function __construct($updateIssue = false)
    {
        if (!$updateIssue) {
            $this->project = new Project();

            $this->assignee = new Reporter();
            $this->priority = new Priority();
            $this->versions = [];

            $this->issuetype = new IssueType();
        }
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function getProjectKey()
    {
        return $this->project->key;
    }

    public function getProjectId()
    {
        return $this->project->id;
    }

    public function getIssueType()
    {
        return $this->issuetype;
    }

    public function setProjectKey($key)
    {
        if(is_null($this->project)) {
            $this->project = new Project();
        }

        $this->project->key = $key;

        return $this;
    }

    public function setProjectId($id)
    {
        if(is_null($this->project)) {
            $this->project = new Project();
        }

        $this->project->id = $id;

        return $this;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function setReporterName($name)
    {
        if (is_null($this->reporter)) {
            $this->reporter = new Reporter();
        }

        $this->reporter->name = $name;

        return $this;
    }

    public function setAssigneeName($name)
    {
        if (is_null($this->assignee)) {
            $this->assignee = new Reporter();
        }

        $this->assignee->name = $name;

        return $this;
    }

    public function setPriorityName($name)
    {
        if (is_null($this->priority)) {
            $this->priority = new Priority();
        }

        $this->priority->name = $name;

        return $this;
    }

    public function addVersion($name)
    {
        if (is_null($this->versions)) {
            $this->versions = [];
        }

        $v = new Version();
        $v->name = $name;

        array_push($this->versions, $v);

        return $this;
    }

    public function addComment($comment)
    {
        if (is_null($this->comment)) {
            $this->comment = [];
        }

        array_push($this->comment, $comment);

        return $this;
    }

    public function addLabel($label)
    {
        if (is_null($this->labels)) {
            $this->labels = [];
        }

        array_push($this->labels, $label);

        return $this;
    }

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
}
