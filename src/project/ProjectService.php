<?php

namespace JiraRestApi\Project;

require 'vendor/autoload.php';

class ProjectService extends \JiraRestApi\JiraClient {	

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

    /**
     * get all project list
     * 
     * @return array of Project class
     */
    public function getAllProjects() {
        $ret = $this->exec('/project', null);        
        
        $prjs = $this->json_mapper->mapArray(
             json_decode($ret, true), new \ArrayObject(), '\JiraRestApi\Project\Project'
        );
        
        return $prjs;        
    }

    public function get($projectIdOrKey) {
    	$ret = $this->exec("/project/$projectIdOrKey", null);

        $this->log->addInfo("Result=" . $ret );

        $prj = $this->json_mapper->map(
             json_decode($ret), new Project()
        );

        return $prj;
    }
}

?>

