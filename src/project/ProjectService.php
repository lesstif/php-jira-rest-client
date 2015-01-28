<?php

namespace JiraRestApi\Project;

require 'vendor/autoload.php';

class ProjectService extends \JiraRestApi\JiraClient {	

	//private $mapper = new JsonMapper();

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

    private $uri = "/project";
 	public function __construct($config, $opt_array = null) {
        parent::__construct($config, $opt_array);        
    }

    public function getAllProjects() {
        return $this->exec('/project', null);
    }

    public function get($project_name) {
    	return $this->exec('/project/$project_name', null);
    }
}

?>

