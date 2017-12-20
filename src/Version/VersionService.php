<?php

namespace JiraRestApi\Version;

use JiraRestApi\Issue\Version;
use JiraRestApi\JiraException;
use JiraRestApi\Project\ProjectService;

class VersionService extends \JiraRestApi\JiraClient
{
    private $uri = '/version';

    /**
     * create version.
     *
     * @see https://docs.atlassian.com/jira/REST/server/#api/2/version-createVersion
     */
    public function create($version)
    {
        throw new JiraException('create version not yet implemented');
    }

    /**
     * Modify a version's sequence within a project.
     *
     * @param $version
     *
     * @throws JiraException
     */
    public function move($version)
    {
        throw new JiraException('move version not yet implemented');
    }

    /**
     * get project version.
     *
     * @param $id version id
     *
     * @return Version
     *
     * @see ProjectService::getVersions()
     */
    public function get($id)
    {
        $ret = $this->exec($this->uri.'/'.$id);

        $this->log->addInfo('Result='.$ret);

        $json = json_decode($ret);
        $results = array_map(function ($elem) {
            return $this->json_mapper->map($elem, new ProjectType());
        }, $json);

        return $results;
    }

    public function update($ver)
    {
        throw new JiraException('update version not yet implemented');
    }

    public function delete($ver)
    {
        throw new JiraException('delete version not yet implemented');
    }

    public function merge($ver)
    {
        throw new JiraException('merge version not yet implemented');
    }

    /**
     * Returns a bean containing the number of fixed in and affected issues for the given version.
     *
     * @param $id int version id
     *
     * @throws JiraException
     *
     * @see https://docs.atlassian.com/jira/REST/server/#api/2/version-getVersionRelatedIssues
     */
    public function getRelatedIssues($id)
    {
        throw new JiraException('get version Related Issues not yet implemented');
    }
}
