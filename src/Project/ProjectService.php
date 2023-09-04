<?php

namespace JiraRestApi\Project;

use JiraRestApi\Component\Component;
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
    public function getAllProjects($paramArray = []): \ArrayObject
    {
        $ret = $this->exec($this->uri.$this->toHttpQueryParameter($paramArray), null);

        $prjs = $this->json_mapper->mapArray(
            json_decode($ret, false),
            new \ArrayObject(),
            Project::class
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
     * @return Project
     */
    public function get($projectIdOrKey): Project
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
     * @param int|string $projectIdOrKey Project Key
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Reporter[]
     */
    public function getAssignable(int|string $projectIdOrKey): array
    {
        $ret = $this->exec("/user/assignable/search?project=$projectIdOrKey", null);
        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new Reporter());
        }, $json);

        return $results;
    }

    /**
     * @param int|string $projectIdOrKey
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return IssueType[]
     */
    public function getStatuses(int|string $projectIdOrKey): array
    {
        $ret = $this->exec($this->uri."/$projectIdOrKey/statuses", null);
        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new IssueType());
        }, $json);

        return $results;
    }

    /**
     * Get the Components defined in a Jira Project.
     *
     * @param int|string $projectIdOrKey
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return \JiraRestApi\Component\Component[]
     */
    public function getProjectComponents(int|string $projectIdOrKey): array
    {
        $ret = $this->exec($this->uri."/$projectIdOrKey/components", null);
        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new Component());
        }, $json);

        return $results;
    }

    /**
     * make transition info array for project issue transition.
     *
     * @param int|string $projectIdOrKey
     *
     * @throws JiraException
     *
     * @return array
     * @return array
     */
    public function getProjectTransitionsToArray(int|string $projectIdOrKey): array
    {
        $ret = $this->exec($this->uri."/$projectIdOrKey/statuses", null);
        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new IssueType());
        }, $json);

        $transitions = [];
        foreach ($results as $issueType) {
            foreach ($issueType->statuses as $status) {
                if (!in_array($status->id, array_column($transitions, 'id'))) {
                    $transitions[] = [
                        'id'               => $status->id,
                        'name'             => $status->name,
                        'untranslatedName' => $status->untranslatedName ?? $status->name,
                    ];
                }
            }
        }

        return $transitions;
    }

    /**
     * @throws \JiraRestApi\JiraException
     *
     * @return ProjectType[]
     */
    public function getProjectTypes(): array
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
     * @param int|string $key
     *
     * @throws \JsonMapper_Exception
     * @throws \JiraRestApi\JiraException
     *
     * @return ProjectType
     */
    public function getProjectType(int|string $key): ProjectType
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
     * @param int|string $key
     *
     * @throws \JsonMapper_Exception
     * @throws \JiraRestApi\JiraException
     *
     * @return ProjectType
     */
    public function getAccessibleProjectType(int|string $key): ProjectType
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
     * @param int|string $projectIdOrKey
     * @param array      $queryParam
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Version[] array of version
     */
    public function getVersionsPagenated(int|string $projectIdOrKey, array $queryParam = []): \ArrayObject
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
     */
    public function getVersions(string $projectIdOrKey): \ArrayObject
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
     * @param int|string $projectIdOrKey
     * @param string     $versionName
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Version version
     */
    public function getVersion(int|string $projectIdOrKey, string $versionName): Version
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
    public function createProject(Project $project): Project
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
     * @throws \JsonMapper_Exception
     * @throws JiraException
     *
     * @return Project
     */
    public function updateProject(Project $project, string|int $projectIdOrKey): Project
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
     * @param int|string $projectIdOrKey
     *
     * @throws JiraException
     *
     * @return string response status
     *
     * STATUS 401 Returned if the user is not logged in.
     * STATUS 204 - application/json Returned if the project is successfully deleted.
     * STATUS 403 - Returned if the currently authenticated user does not have permission to delete the project.
     * STATUS 404 - Returned if the project does not exist.
     */
    public function deleteProject(int|string $projectIdOrKey): string
    {
        $ret = $this->exec($this->uri.'/'.$projectIdOrKey, null, 'DELETE');

        return $ret;
    }

    /**
     * Archive a project only available for premium subscription.
     *
     * @param int|string $projectIdOrKey
     *
     * @throws JiraException
     *
     * @return string response status
     *
     * STATUS 401 Returned if the user is not logged in.
     * STATUS 204 - application/json Returned if the project is successfully archived.
     * STATUS 403 - Returned if the currently authenticated user does not have permission to archive the project.
     * STATUS 404 - Returned if the project does not exist.
     * STATUS 405 - Method not allowed specified request HTTP method was received and recognized by the server, but is not supported by the target resource.
     */
    public function archiveProject(int|string $projectIdOrKey): string
    {
        $ret = $this->exec($this->uri.'/'.$projectIdOrKey.'/archive', null, 'PUT');

        return $ret;
    }

    /**
     * Get all the Roles of a Jira Project.
     *
     * @param int|string $projectIdOrKey
     *
     * @throws JiraException
     *
     * @return string
     */
    public function getRolesOfProject(int|string $projectIdOrKey): string
    {
        return $this->exec($this->uri.'/'.$projectIdOrKey.'/role', null, 'PUT');
    }

    /**
     * Assign a Role to a Project.
     *
     * @param int|string $projectIdOrKey
     * @param int        $roleId
     *
     * @throws JiraException
     *
     * @return string
     */
    public function assignRoleToProject(int|string $projectIdOrKey, int $roleId): string
    {
        return $this->exec($this->uri.'/'.$projectIdOrKey.'/role/'.$roleId, null, 'PUT');
    }

    /**
     * Add Role Actor to a Project Role.
     *
     * @param int|string $projectIdOrKey
     * @param int        $roleId
     * @param string     $actor
     *
     * @throws JiraException
     *
     * @return string
     */
    public function addProjectRoleActors(int|string $projectIdOrKey, int $roleId, string $actor): string
    {
        return $this->exec($this->uri.'/'.$projectIdOrKey.'/role/'.$roleId, $actor, 'POST');
    }
}
