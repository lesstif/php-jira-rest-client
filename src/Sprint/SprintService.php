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
    private $uri = '/rest/agile/1.0/sprint';

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
        $this->restClient->setAPIUri('');
    }


    static function getSprintFromJSON($json)
    {
        $json_mapper = new \JsonMapper();
        $json_mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];
        return $json_mapper->map($json, new Sprint() );
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
        $ret = $this->restClient->exec($this->uri.'/'.$sprintId);
        return $this->getSprintFromJSON(json_decode($ret));
    }

    public function getVelocityForSprint($sprintID){
        try {
            $sprint = $this->getSprint($sprintID);
            if (!is_null($sprint->originBoardId)){
                $ret = $this->restClient->exec('/rest/greenhopper/1.0/rapid/charts/velocity.json?rapidViewId='.$sprint->originBoardId.'&sprintId='.$sprint->id);
                $velocityObject = json_decode($ret);
                $velocityStats = $velocityObject->{'velocityStatEntries'};
                if (property_exists($velocityStats,$sprint->id)) {
                    $sprint->estimatedVelocity = $velocityStats->{$sprint->id}->{'estimated'}->value;
                    $sprint->completedVelocity = $velocityStats->{$sprint->id}->{'completed'}->value;
                } else {
                    $sprint->estimatedVelocity = null;
                    $sprint->completedVelocity = null;
                }
            }
            return $sprint;
        }
        catch (JiraException $e) {
            print("Error Occured! " . $e->getMessage());
            return null;
        }
    }

}
