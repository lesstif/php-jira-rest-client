<?php

namespace JiraRestApi\Version;

use JiraRestApi\Issue\Version;
use JiraRestApi\Issue\VersionIssueCounts;
use JiraRestApi\Issue\VersionUnresolvedCount;
use JiraRestApi\JiraException;
use JiraRestApi\Project\ProjectService;

class VersionService extends \JiraRestApi\JiraClient
{
    private $uri = '/version';

    /**
     * Function to create a new project version.
     *
     * @param Version|array $version
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Version Version class
     */
    public function create($version)
    {
        if ($version->releaseDate instanceof \DateTimeInterface) {
            $version->releaseDate = $version->releaseDate->format('Y-m-d');
        }
        $data = json_encode($version);

        $this->log->info("Create Version=\n".$data);

        $ret = $this->exec($this->uri, $data, 'POST');

        return $this->json_mapper->map(
            json_decode($ret),
            Version::class
        );
    }

    /**
     * Modify a version's sequence within a project.
     *
     * @param Version $version
     *
     * @throws JiraException
     */
    public function move(Version $version)
    {
        throw new JiraException('move version not yet implemented');
    }

    /**
     * get project version.
     *
     * @param string $id version id
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Version
     *
     * @see ProjectService::getVersions()
     */
    public function get(string $id)
    {
        $ret = $this->exec($this->uri.'/'.$id);

        $this->log->info('Result='.$ret);

        return $this->json_mapper->map(
            json_decode($ret),
            Version::class
        );
    }

    /**
     * @author Martijn Smidt <martijn@squeezely.tech>
     *
     * @param Version $version
     *
     * @throws JiraException
     *
     * @return Version
     */
    public function update(Version $version): Version
    {
        if (!$version->id || !is_numeric($version->id)) {
            throw new JiraException($version->id.' is not a valid version id.');
        }

        // avoid weird error "Only one of 'releaseDate' and 'userReleaseDate' can be specified when editing a version."
        $version->userReleaseDate = null;
        $version->userStartDate = null;

        $data = json_encode($version);

        $ret = $this->exec($this->uri.'/'.$version->id, $data, 'PUT');

        return $this->json_mapper->map(
            json_decode($ret),
            Version::class
        );
    }

    /**
     * @author Martijn Smidt <martijn@squeezely.tech>
     *
     * @param Version      $version
     * @param Version|bool $moveAffectedIssuesTo
     * @param Version|bool $moveFixIssuesTo
     *
     * @throws JiraException
     *
     * @return string
     */
    public function delete(Version $version, $moveAffectedIssuesTo = false, $moveFixIssuesTo = false)
    {
        if (!$version->id || !is_numeric($version->id)) {
            throw new JiraException($version->id.' is not a valid version id.');
        }

        $data = [];

        if ($moveAffectedIssuesTo && $moveAffectedIssuesTo instanceof Version) {
            $data['moveAffectedIssuesTo'] = $moveAffectedIssuesTo->name;
        }

        if ($moveFixIssuesTo && $moveFixIssuesTo instanceof Version) {
            $data['moveFixIssuesTo'] = $moveFixIssuesTo->name;
        }

        $ret = $this->exec($this->uri.'/'.$version->id, json_encode($data), 'DELETE');

        return $ret;
    }

    public function merge($ver)
    {
        throw new JiraException('merge version not yet implemented');
    }

    /**
     * Returns a bean containing the number of fixed in and affected issues for the given version.
     *
     * @param Version $version
     *
     * @throws JiraException
     *
     * @see https://docs.atlassian.com/jira/REST/server/#api/2/version-getVersionRelatedIssues
     */
    public function getRelatedIssues(Version $version)
    {
        if (!$version->id || !is_numeric($version->id)) {
            throw new JiraException($version->id.' is not a valid version id.');
        }

        $ret = $this->exec($this->uri.'/'.$version->id.'/relatedIssueCounts');

        return $this->json_mapper->map(
            json_decode($ret),
            new VersionIssueCounts()
        );
    }

    /**
     * Returns a bean containing the number of unresolved issues for the given version.
     *
     * @param Version $version
     *
     * @throws JiraException
     *
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/version-getVersionUnresolvedIssues
     *
     * @return VersionUnresolvedCount
     */
    public function getUnresolvedIssues(Version $version)
    {
        if (!$version->id || !is_numeric($version->id)) {
            throw new JiraException($version->id.' is not a valid version id.');
        }

        $ret = $this->exec($this->uri.'/'.$version->id.'/unresolvedIssueCount');

        return $this->json_mapper->map(
            json_decode($ret),
            new VersionUnresolvedCount()
        );
    }
}
