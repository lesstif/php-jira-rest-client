<?php

namespace JiraRestApi\Board;

use JiraRestApi\JiraClient;


class BoardService
{
    private $uri = '/board';
    protected $restClient;

    public function __construct(ConfigurationInterface $configuration = null, Logger $logger = null, $path = './')
    {

        $this->restClient = new JiraClient($configuration, $logger, $path);

        $this->restClient->setAPIUri('/rest/agile/1.0');
    }

    public function getBoardFromJSON($json) {
        return $this->restClient->json_mapper->map(
          $json, new Board()
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
        //TODO look at re-writting this method to better allow for testing.
        $boards = $this->restClient->json_mapper
            ->mapArray($boardArray,
                      new \ArrayObject(),
                        '\JiraRestApi\Board\Board'
        );
        return $boards;
    }
    /**
     *  get a Board
     *
     * @param integer $boardId
     *
     * @return object
     */
    public function getFilteredBoard($filter)
    {
        $ret = $this->restClient->exec($this->uri.'/?='.$filter, null);
        return $this->getBoardFromJSON(json_decode($ret));
    }
}
