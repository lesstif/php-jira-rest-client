<?php

namespace JiraRestApi\Board;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Issue\Issue;

class BoardService extends \JiraRestApi\JiraClient
{
    private $uri = '/board';

    public function __construct(ConfigurationInterface $configuration = null, Logger $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setAPIUri('/rest/agile/1.0');
    }

    /**
     * get all project list.
     *
     * @param array $paramArray
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Project[] array of Project class
     */
    public function getBoardList($paramArray = [])
    {
        $json = $this->exec($this->uri.$this->toHttpQueryParameter($paramArray), null);
        $boards = $this->json_mapper->mapArray(
            json_decode($json)->values, new \ArrayObject(), Board::class
        );
        return $boards;
    }

    public function getBoard($id, $paramArray = [])
    {
        $json = $this->exec($this->uri.'/'.$id.$this->toHttpQueryParameter($paramArray), null);
        $board = $this->json_mapper->map(
            json_decode($json), new Board()
        );
        return $board;
    }

    public function getBoardIssues($id, $paramArray = [])
    {
        $json = $this->exec($this->uri.'/'.$id . $this->toHttpQueryParameter($paramArray), null);
        $board = $this->json_mapper->mapArray(
            json_decode($json), new \ArrayObject(), Issue::class
        );
        return $board;
    }

}
