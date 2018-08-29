<?php

namespace JiraRestApi\Board;

use JiraRestApi\JiraClient;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Sprint\SprintService;
use Monolog\Logger;



class BoardService
{
    private $uri = '/rest/agile/1.0/board';
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


    static function getBoardFromJSON($json) {
      $json_mapper = new \JsonMapper();
      $json_mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];
      return $json_mapper->map($json, new Board() );
    }
    static function getArrayOfBoardsFromJSON($boardList) {
      $json_mapper = new \JsonMapper();
      $json_mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];
      return $json_mapper->mapArray($boardList,
                                    new \ArrayObject(),
                                    '\JiraRestApi\Board\Board'
                                  );

    }

    /**
     *  get a Board
     *
     * @param integer $boardId
     *
     * @return object
     */
    public function getBoard($boardId)
    {
        $ret = $this->restClient->exec('/rest/agile/1.0/board/'.$boardId, null);
        return $this->getBoardFromJSON(json_decode($ret));
    }
    /**
     *  get all Boards
     *
     * @param integer $boardId
     *
     * @return object
     */
    public function getAllBoards($paramArray = [])
    {
        $boardArray = array();
        $boardArray = $this->loopOverResults($this->uri,$paramArray);


        // print $results
        return $this->getArrayOfBoardsFromJSON($boardArray);
    }

    public function getBoardWithSprintsList($boardId,$paramArray = []){
      $board = $this->getBoard($boardId);
      $sprintList = $this->loopOverResults($this->uri.'/'.$boardId.'/sprint/',$paramArray);
      foreach ($sprintList as $sprint) {
        $sprintObjectsArray[] = SprintService::getSprintFromJSON($sprint);
      }
      $board->sprintList = $sprintObjectsArray;
      return $board;
    }

    public function loopOverResults($uri,$paramArray = []) {
      $resultsArray = array();
      $ret = $this->restClient->exec($uri.$this->restClient->toHttpQueryParameter($paramArray), null);
      $results = json_decode($ret);
      while (!$results->isLast) {
        $resultsArray = array_merge($resultsArray,$results->values);
        $paramArray['startAt'] = $results->startAt + $results->maxResults;
        $ret = $this->restClient->exec($this->uri.$this->restClient->toHttpQueryParameter($paramArray), null);
        $results = json_decode($ret);
      }
      $resultsArray = array_merge($resultsArray,$results->values);
      return $resultsArray;
    }

}
