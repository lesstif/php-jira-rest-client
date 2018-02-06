<?php

namespace JiraRestApi\Project;

use JiraRestApi\Issue\IssueType;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\Version;

class ProjectService extends \JiraRestApi\JiraClient
{
    private $uri = '/project';

    /**
     * get all project list.
     *
     * @param array $paramArray
     * @throws \JiraRestApi\JiraException
     *
     * @return Project[] array of Project class
     */
    public function getAllProjects($paramArray = [])
    {
        $ret = $this->exec($this->uri.$this->toHttpQueryParameter($paramArray), null);

        $prjs = $this->json_mapper->mapArray(
            json_decode($ret, false), new \ArrayObject(), '\JiraRestApi\Project\Project'
        );

        return $prjs;
    }

    /**
     * get Project id By Project Key.
     * throws HTTPException if the project is not found, or the calling user does not have permission or view it.
     *
     * @param string|int $projectIdOrKey projectName or Project Key(Ex: Test, MyProj)
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Project|object
     */
    public function get($projectIdOrKey)
    {
        $ret = $this->exec($this->uri."/$projectIdOrKey", null);

        $this->log->addInfo('Result='.$ret);

        $prj = $this->json_mapper->map(
            json_decode($ret), new Project()
        );

        return $prj;
    }

    /**
     * get assignable Users for a given project.
     * throws HTTPException if the project is not found, or the calling user does not have permission or view it.
     *
     * @param string|int projectIdOrKey Project Key
     *
     * @throws \JiraRestApi\JiraException
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

    /**
     * @param string|int $projectIdOrKey
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return IssueType[]
     */
    public function getStatuses($projectIdOrKey)
    {
        $ret = $this->exec($this->uri."/$projectIdOrKey/statuses", null);
        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new IssueType());
        }, $json);

        return $results;
    }

    /**
     * @throws \JiraRestApi\JiraException
     *
     * @return ProjectType[]
     */
    public function getProjectTypes()
    {
        $ret = $this->exec($this->uri.'/type');

        $this->log->addInfo('Result='.$ret);

        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new ProjectType());
        }, $json);

        return $results;
    }

    /**
     * @param string|int $key
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return ProjectType|object
     */
    public function getProjectType($key)
    {
        $ret = $this->exec($this->uri."/type/$key");

        $this->log->addInfo('Result='.$ret);

        $type = $this->json_mapper->map(
            json_decode($ret, false), new ProjectType()
        );

        return $type;
    }

    /**
     * @param string|int $key
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return ProjectType|object
     */
    public function getAccessibleProjectType($key)
    {
        $ret = $this->exec($this->uri."/type/$key/accessible");

        $this->log->addInfo('Result='.$ret);

        $type = $this->json_mapper->map(
            json_decode($ret, false), new ProjectType()
        );

        return $type;
    }

    /**
     * get pagenated Project versions.
     *
     * @param string|int $projectIdOrKey
     * @param array      $queryParam
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Version[] array of version
     */
    public function getVersionsPagenated($projectIdOrKey, $queryParam = [])
    {
        $default = [
            'startAt'    => 0,
            'maxResults' => 50,
            // order by following field: sequence, name, startDate, releaseDate
            //'orderBy' => null,
            //'expand' => null,
        ];

        $param = $this->toHttpQueryParameter(
            array_merge($default, $queryParam)
        );

        $ret = $this->exec($this->uri."/$projectIdOrKey/version".$param);

        $this->log->addInfo('Result='.$ret);

        //@see https://docs.atlassian.com/jira/REST/server/#api/2/project-getProjectVersions
        $json = json_decode($ret);

        $prjs = $this->json_mapper->mapArray(
            $json->values, new \ArrayObject(), '\JiraRestApi\Issue\Version'
        );

        return $prjs;
    }

    /**
     * get specified's project versions.
     *
     * @param string|int $projectIdOrKey
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Version[] array of version
     */
    public function getVersions($projectIdOrKey)
    {
        $ret = $this->exec($this->uri."/$projectIdOrKey/versions");

        $this->log->addInfo('Result='.$ret);

        $prjs = $this->json_mapper->mapArray(
            json_decode($ret, false), new \ArrayObject(), '\JiraRestApi\Issue\Version'
        );

        return $prjs;
    }
}
