<?php

namespace JiraRestApi\Issue;

class IssueType {	
	/**
     * IssueType URI
     * @var string
     */
    public $self;

    /**
     * IssueType id
     * @var string
     */
    public $id;

    /**
     * IssueType description
     * @var string
     */
    public $description;

	/**
     * IssueType name
     * @var string
     */
    public $name;

   	/**
     * is subtask
     * @var boolean
     */
    public $subtak;   
}

?>