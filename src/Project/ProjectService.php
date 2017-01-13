<?php

namespace JiraRestApi\Project;

use JiraRestApi\Issue\IssueType;
use JiraRestApi\Issue\Reporter;

class ProjectService extends \JiraRestApi\JiraClient
{
    private $uri = '/project';

    /**
     * get all project list.
     *
     * @return array of Project class
     */
    public function getAllProjects()
    {
        $ret = $this->exec($this->uri, null);

        $prjs = $this->json_mapper->mapArray(
            json_decode($ret, false), new \ArrayObject(), '\JiraRestApi\Project\Project'
        );

        return $prjs;
    }

    /**
     * get Project id By Project Key.
     *
     * @param projectName Project Key(Ex: Test, MyProj)
     *
     * @throws HTTPException if the project is not found, or the calling user does not have permission or view it.
     *
     * @return string project id
     */
    public function get($projectIdOrKey)
    {
        $ret = $this->exec($this->uri . "/$projectIdOrKey", null);

        $this->log->addInfo('Result=' . $ret);

        $prj = $this->json_mapper->map(
            json_decode($ret), new Project()
        );

        return $prj;
    }

    /**
     * get assignable Users for a given project.
     *
     * @param projectIdOrKey Project Key
     *
     * @throws HTTPException if the project is not found, or the calling user does not have permission or view it.
     *
     * @return Reporter[]
     */
    public function getAssignable($projectIdOrKey)
    {
        $ret = $this->exec("/user/assignable/search?project=$projectIdOrKey", null);
        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new Reporter());
        }, $json);

        return $results;
    }

    public function getStatuses($projectIdOrKey)
    {
        $ret = $this->exec($this->uri . "/$projectIdOrKey/statuses", null);
        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new IssueType());
        }, $json);

        return $results;
    }

    /**
     * @return ProjectType[]
     */
    public function getProjectTypes()
    {
        $ret = $this->exec($this->uri . "/type");

        $this->log->addInfo('Result=' . $ret);

        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new ProjectType());
        }, $json);

        return $results;
    }

    /**
     * @param string|int $key
     * @return ProjectType
     */
    public function getProjectType($key)
    {
        $ret = $this->exec($this->uri . "/type/$key");

        $this->log->addInfo('Result=' . $ret);

        $type = $this->json_mapper->map(
            json_decode($ret, false), new ProjectType()
        );

        return $type;
    }

    /**
     * @param string|int $key
     * @return ProjectType
     */
    public function getAccessibleProjectType($key)
    {
        $ret = $this->exec($this->uri . "/type/$key/accessible");

        $this->log->addInfo('Result=' . $ret);

        $type = $this->json_mapper->map(
            json_decode($ret, false), new ProjectType()
        );

        return $type;
    }
}
