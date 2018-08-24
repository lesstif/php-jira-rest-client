<?php
/**
 * Created by PhpStorm.
 * User: meshulam
 * Date: 11/08/2017
 * Time: 17:28.
 */

namespace JiraRestApi\Sprint;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\JiraClient;
use JiraRestApi\JiraException;
use Monolog\Logger;

class SprintService
{
    //https://jira01.devtools.intel.com/rest/agile/1.0/board?projectKeyOrId=34012
    private $uri = '/sprint';


    protected $restClient;

    public function __construct(ConfigurationInterface $configuration = null, Logger $logger = null, $path = './')
    {
        $this->restClient = new JiraClient($configuration, $logger, $path);
        $this->restClient->setAPIUri('/rest/agile/1.0');
    }

    public function getSprintFromJSON($json)
    {
        $sprint = $this->restClient->json_mapper->map(
            $json, new Sprint()
        );

        return $sprint;
    }

    /**
     *  get all Sprint list.
     *
     * @param integer $sprintId
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return object
     */
    public function getSprint($sprintId)
    {

        $ret = $this->restClient->exec($this->uri.'/'.$sprintId, null);

        return $sprint = $this->restClient->json_mapper->map(
            json_decode($ret), new Sprint()
        );
    }
}
