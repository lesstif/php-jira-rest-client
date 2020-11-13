<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: meshulam
 * Date: 11/08/2017
 * Time: 17:28.
 */

namespace JiraRestApi\Sprint;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Exceptions\JiraException;
use JiraRestApi\Issue\Issue;
use JiraRestApi\JiraClient;
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

    /**
     * @param object $json JSON object structure from json_decode
     *
     * @throws \JsonMapper_Exception
     *
     * @return Sprint
     */
    public function getSprintFromJSON(object $json): Sprint
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
     * @param string $sprintId
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Sprint
     */
    public function getSprint(string $sprintId): Sprint
    {
        $ret = $this->exec($this->uri.'/'.$sprintId, null);

        $this->log->info("Result=\n".$ret);

        return $sprint = $this->json_mapper->map(
            json_decode($ret),
            new Sprint()
        );
    }

    /**
     * @param string|int $sprintId
     * @param array      $paramArray
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Issue[] array of Issue
     */
    public function getSprintIssues($sprintId, array $paramArray = []): array
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
