<?php

namespace JiraRestApi\Board;

use JiraRestApi\JiraClient;
use JiraRestApi\Configuration\ConfigurationInterface;
use Monolog\Logger;



class BoardService
{
    private $uri = '/board';
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


    public function getBoardFromJSON($json) {
      $json_mapper = new \JsonMapper();
      $json_mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];
      return $json_mapper->map($json, new Board() );
    }
    public function getArrayOfBoardsFromJSON($boardList) {
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
        $ret = $this->restClient->exec($this->uri.'/'.$boardId, null);
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
        $ret = $this->restClient->exec($this->uri.$this->restClient->toHttpQueryParameter($paramArray), null);
        $boardResults = json_decode($ret);
        while (!$boardResults->isLast) {
          $boardArray = array_merge($boardArray,$boardResults->values);
          $paramArray['startAt'] = $boardResults->startAt + $boardResults->maxResults;
          $ret = $this->restClient->exec($this->uri.$this->restClient->toHttpQueryParameter($paramArray), null);
          $boardResults = json_decode($ret);
        }
        $boardArray = array_merge($boardArray,$boardResults->values);

        // print $boardResults
        return $this->getArrayOfBoardsFromJSON($boardArray);
    }
}
