<?php

namespace JiraRestApi\Issue;

class IssueField {
	public function __construct() {
        $this->project = new \JiraRestApi\Project\Project();
     
        $this->assignee = new \JiraRestApi\Issue\Reporter();
        $this->priority = new \JiraRestApi\Issue\Priority();
        $this->versions = array();

        $this->issuetype = new \JiraRestApi\Issue\IssueType();
    }

    public function setProjectName($name) {
    	$this->project->name = $name;
    	return $this;
    }
    public function setProjectId($id) {
    	$this->project->id = $id;
    	return $this;
    }

    public function setIssueType($name) {
    	$this->issuetype->name = $name;
    	return $this;
    }

    public function setSummary($summary) {
    	$this->summary = $summary;
    	return $this;
    }

    public function setReporterName($name) {
    	if (is_null($this->reporter))
    	  $this->reporter = new \JiraRestApi\Issue\Reporter();

    	$this->reporter->name = $name;
    	return $this;
    }

    public function setAssigneeName($name) {
    	$this->assignee->name = $name;
    	return $this;
    }

    public function setPriorityName($name) {
    	$this->priority->name = $name;
    	return $this;
    }

    public function setDescription($description) {
    	$this->description = $description;
    	return $this;
    }

    public function addVersion($id, $name) {
    	$v = new Version();

    	if (isset($id))
    		$v->id = $id;
    	if (isset($name))
    		$v->name = $name;

    	array_push($this->versions, $v);
    	return $this;
    }

	/** @var string */
	public $summary;

	/** @var string */
	public $progress;

	/** @var string */
	public $timetracking;

	/** @var IssueType */
	public $issuetype;

	/** @var string */
	public $timespent;
	
	/** @var Reporter */
	public $reporter;

	/** @var DateTime */
	public $created;

	/** @var DateTime */
	public $updated;

	/** @var string */
	public $description;

	/** @var Priority */
	public $priority;
	
	/** @var IssueStatus */
	public $status;

	/** @var string */
	public $labels;

	/** @var JiraRestApi\Project\Project */
	public $project;

	/** @var string */
	public $environment;
	
	/** @var string */
	public $components;

	/** @var Comments */
	public $comment;
	
	/** @var string */
	public $votes;

	/** @var string */
	public $resolution;

	/** @var string */
	public $fixVersions;

	/** @var Reporter */
	public $creator;

	/** @var string */
	public $watches;

	/** @var string */
	public $worklog;

	/** @var Reporter */
	public $assignee;

	/* @var VersionList[\JiraRestApi\Issue\Version] */
	public $versions;
}

?>