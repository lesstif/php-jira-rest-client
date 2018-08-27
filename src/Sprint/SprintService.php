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
    private $uri = '/sprint';

    protected $restClient;

    public function __construct(ConfigurationInterface $configuration = null, Logger $logger = null, $path = './', JiraClient $jiraClient = null)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->path = $path;
        $this->restClient = $jiraClient;
        if (!$this->restClient) {
          $this->setRestClient();
        }

    }

    public function setRestClient(){
      $this->restClient = new JiraClient($this->configuration, $this->logger, $this->path);
      $this->restClient->setAPIUri('/rest/agile/1.0');
    }


    public function getSprintFromJSON($json)
    {

        $this->json_mapper = new \JsonMapper();

        // Fix "\JiraRestApi\JsonMapperHelper::class" syntax error, unexpected 'class' (T_CLASS), expecting identifier (T_STRING) or variable (T_VARIABLE) or '{' or '$'
        $this->json_mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];

        $sprint = $this->json_mapper->map($json, new Sprint() );

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
        return $this->getSprintFromJSON(json_decode($ret));
    }
}
