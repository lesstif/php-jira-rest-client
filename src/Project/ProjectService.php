<?php

namespace JiraRestApi\Project;

use JiraRestApi\Issue\IssueType;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\Version;
use JiraRestApi\JiraException;

class ProjectService extends \JiraRestApi\JiraClient
{
    private $uri = '/project';

    /**
     * get all project list.
     *
     * @param array $paramArray
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Project[] array of Project class
     */
    public function getAllProjects($paramArray = [])
    {
        $ret = $this->exec($this->uri.$this->toHttpQueryParameter($paramArray), null);

        $prjs = $this->json_mapper->mapArray(
            json_decode($ret, false),
            new \ArrayObject(),
            '\JiraRestApi\Project\Project'
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

        $this->log->info('Result='.$ret);

        $prj = $this->json_mapper->map(
            json_decode($ret),
            new Project()
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

        $this->log->info('Result='.$ret);

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

        $this->log->info('Result='.$ret);

        $type = $this->json_mapper->map(
            json_decode($ret, false),
            new ProjectType()
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

        $this->log->info('Result='.$ret);

        $type = $this->json_mapper->map(
            json_decode($ret, false),
            new ProjectType()
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

        $this->log->info('Result='.$ret);

        //@see https://docs.atlassian.com/jira/REST/server/#api/2/project-getProjectVersions
        $json = json_decode($ret);

        $versions = $this->json_mapper->mapArray(
            $json->values,
            new \ArrayObject(),
            '\JiraRestApi\Issue\Version'
        );

        return $versions;
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

        $this->log->info('Result='.$ret);

        $versions = $this->json_mapper->mapArray(
            json_decode($ret, false),
            new \ArrayObject(),
            '\JiraRestApi\Issue\Version'
        );

        return $versions;
    }

    /**
     * get specified's project version.
     *
     * @param string|int $projectIdOrKey
     * @param string     $versionName
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Version version
     */
    public function getVersion($projectIdOrKey, $versionName)
    {
        $ret = $this->exec($this->uri."/$projectIdOrKey/versions");

        $this->log->info('Result='.$ret);

        $versions = $this->json_mapper->mapArray(
            json_decode($ret, false),
            new \ArrayObject(),
            '\JiraRestApi\Issue\Version'
        );

        foreach ($versions as $v) {
            if ($v->name === $versionName) {
                return $v;
            }
        }

        throw new JiraException("Can't found version \"$versionName\" in the Project \"$projectIdOrKey\"");
    }

    /**
     * Creates a new project.
     *
     * @param Project $project
     *
     * @throws JiraException
     *
     * @return Project project
     */
    public function createProject($project)
    {
        $data = json_encode($project);

        $ret = $this->exec($this->uri, $data, 'POST');

        $this->log->info('createProject Result='.$ret);

        return $this->json_mapper->map(
            json_decode($ret),
            new Project()
        );
    }

    /**
     * Updates a project.
     *
     * Only non null values sent in JSON will be updated in the project.
     * Values available for the assigneeType field are: "PROJECT_LEAD" and "UNASSIGNED".
     *
     * @param Project $project
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Project project
     */
    public function updateProject($project, $projectIdOrKey)
    {
        $data = json_encode($project);

        $ret = $this->exec($this->uri.'/'.$projectIdOrKey, $data, 'PUT');

        $this->log->info('updateProject Result='.$ret);

        return $this->json_mapper->map(
            json_decode($ret),
            new Project()
        );
    }

    /**
     * @param string $projectIdOrKey
     *
     * @throws JiraException
     *
     * @return int response status
     *
     * STATUS 401 Returned if the user is not logged in.
     * STATUS 204 - application/json Returned if the project is successfully deleted.
     * STATUS 403 - Returned if the currently authenticated user does not have permission to delete the project.
     * STATUS 404 - Returned if the project does not exist.
     */
    public function deleteProject($projectIdOrKey)
    {
        $ret = $this->exec($this->uri.'/'.$projectIdOrKey, null, 'DELETE');

        return $ret;
    }
}
