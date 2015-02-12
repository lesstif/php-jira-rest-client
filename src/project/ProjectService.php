<?php

namespace JiraRestApi\Project;

class ProjectService extends \JiraRestApi\JiraClient {	
    private $uri = "/project";

 	public function __construct() {
        parent::__construct(getConfig());        
    }

    /**
     * get all project list
     * 
     * @return array of Project class
     */
    public function getAllProjects() {
        $ret = $this->exec($this->uri, null);        
        
        $prjs = $this->json_mapper->mapArray(
             json_decode($ret, true), new \ArrayObject(), '\JiraRestApi\Project\Project'
        );
        
        return $prjs;        
    }

     /**
     * get Project id By Project Key 
     * 
     * @param projectName Project Key(Ex: Test, MyProj)
     * 
     * @throws HTTPException if the project is not found, or the calling user does not have permission or view it.
     * 
     * @return string project id
     * 
     */
    public function get($projectIdOrKey) {
    	$ret = $this->exec($this->uri . "/$projectIdOrKey", null);

        $this->log->addInfo("Result=" . $ret );

        $prj = $this->json_mapper->map(
             json_decode($ret), new Project()
        );

        return $prj;
    }
}

?>

