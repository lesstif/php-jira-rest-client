<?php

namespace JiraRestApi\Project;

require 'vendor/autoload.php';

class Project {	

	/**
     * Project URI
     * @var string
     */
    public $self;

    /**
     * Project id
     * @var string
     */
    public $id;

     /**
     * Project key
     * @var string
     */
     public $key;

     /**
     * Project name
     * @var string
     */
     public $name;

     /**
     * avatar URL
     * @var array
     */
     public $avatarUrls;

     /**
     * Project category
     * @var array
     */
     public $projectCategory;

     /* @var string */
     public $description;
}

?>