<?php
/**
 * Created by PhpStorm.
 * User: meshulam
 * Date: 11/08/2017
 * Time: 17:28.
 */

namespace JiraRestApi\Sprint;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Issue\Issue;
use JiraRestApi\JiraClient;
use JiraRestApi\JiraException;
use Psr\Log\LoggerInterface;

class SprintService extends JiraClient
{
    //https://jira01.devtools.intel.com/rest/agile/1.0/board?projectKeyOrId=34012
    private $uri = '/sprint';

    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setAPIUri('/rest/agile/1.0');
    }

    public function getSprintFromJSON($json)
    {
        $sprint = $this->json_mapper->map(
            $json,
            new Sprint()
        );

        return $sprint;
    }

    /**
     *  get all Sprint list.
     *
     * @param Sprint $sprintObject
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return object
     */
    public function getSprint($sprintId)
    {
        $ret = $this->exec($this->uri.'/'.$sprintId, null);

        $this->log->info("Result=\n".$ret);

        return $sprint = $this->json_mapper->map(
            json_decode($ret),
            new Sprint()
        );
    }

    public function getSprintIssues($sprintId, $paramArray = [])
    {
        $json = $this->exec($this->uri.'/'.$sprintId.'/issue'.$this->toHttpQueryParameter($paramArray), null);

        $issues = $this->json_mapper->mapArray(
            json_decode($json)->issues,
            new \ArrayObject(),
            Issue::class
        );

        return $issues;
    }
}
