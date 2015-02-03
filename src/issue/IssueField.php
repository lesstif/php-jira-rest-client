<?php

namespace JiraRestApi\Issue;

class IssueField {
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
	
	/** @var string */
	public $reporter;

	/** @var DateTime */
	public $created;

	/** @var DateTime */
	public $updated;

	/** @var string */
	public $description;

	/** @var string */
	public $priority;
	
	/** @var IssueStatus */
	public $status;

	/** @var string */
	public $labels;

	/** @var string */
	public $project;

	/** @var string */
	public $environment;
	
	/** @var string */
	public $components;
	
	/** @var string */
	public $versions;

	
}

?>