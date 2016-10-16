<?php

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

class IssueField implements \JsonSerializable
{
    use ClassSerialize;

    public function __construct($updateIssue = false)
    {
        if ($updateIssue != true) {
            $this->project = new \JiraRestApi\Project\Project();

            $this->assignee = new \JiraRestApi\Issue\Reporter();
            $this->priority = new \JiraRestApi\Issue\Priority();
            $this->versions = array();

            $this->issuetype = new \JiraRestApi\Issue\IssueType();
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
            $this->reporter = new \JiraRestApi\Issue\Reporter();
        }

        $this->reporter->name = $name;

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
            $this->assignee = new \JiraRestApi\Issue\Reporter();
        }

        $this->assignee->name = $name;

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
            $this->priority = new \JiraRestApi\Issue\Priority();
        }

        $this->priority->name = $name;

        return $this;
    }

    /**
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
     * @param string $name
     *
     * @return $this
     */
    public function addVersion($name)
    {
        if (is_null($this->versions)) {
            $this->versions = array();
        }

        $v = new Version();
        $v->name = $name;
        array_push($this->versions, $v);

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
            $this->labels = array();
        }

        array_push($this->labels, $label);

        return $this;
    }

    /**
     * set issue type.
     *
     * @param IssueType $name
     *
     * @return $this
     */
    public function setIssueType($name)
    {
        if (is_string($name)) {
            if (is_null($this->issuetype)) {
                $this->issuetype = new \JiraRestApi\Issue\IssueType();
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
     * add custom field.
     *
     * @param array $data
     */
    public function addCustomFields($data)
    {
        foreach ($data as $key => $value) {
            if (substr($key, 0, 12) == 'customfield_') {
                $this->{$key} = $value;
            }
        }
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
    }

    public function setParent(Issue $parent)
    {
        $this->parent = $parent;
    }

    /** @var string */
    public $summary;

    /** @var array */
    public $progress;

    /** @var TimeTracking */
    public $timeTracking;

    /** @var IssueType */
    public $issuetype;

    /** @var string|null */
    public $timespent;

    /** @var Reporter */
    public $reporter;

    /** @var \DateTime */
    public $created;

    /** @var \DateTime */
    public $updated;

    /** @var string|null */
    public $description;

    /** @var Priority|null */
    public $priority;

    /** @var IssueStatus */
    public $status;

    /** @var array */
    public $labels;

    /** @var \JiraRestApi\Project\Project */
    public $project;

    /** @var string|null */
    public $environment;

    /** @var array */
    public $components;

    /** @var Comments */
    public $comment;

    /** @var object */
    public $votes;

    /** @var object|null */
    public $resolution;

    /** @var array */
    public $fixVersions;

    /** @var Reporter */
    public $creator;

    /** @var object */
    public $watches;

    /** @var object */
    public $worklog;

    /** @var Reporter|null */
    public $assignee;

    /** @var \JiraRestApi\Issue\Version[] */
    public $versions;

    /** @var \JiraRestApi\Issue\Attachment[] */
    public $attachment;

    /** @var  string|null */
    public $aggregatetimespent;

    /** @var  string|null */
    public $timeestimate;

    /** @var  string|null */
    public $aggregatetimeoriginalestimate;

    /** @var  string|null */
    public $resolutiondate;

    /** @var \DateTime|null */
    public $duedate;

    /** @var array */
    public $issuelinks;

    /** @var Issue[] */
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
}
