<?php

namespace JiraRestApi\Board;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Epic\Epic;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Sprint\Sprint;
use Psr\Log\LoggerInterface;

class BoardService extends \JiraRestApi\JiraClient
{
    private $uri = '/board';

    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setAPIUri('/rest/agile/1.0');
    }

    /**
     * get all boards list.
     *
     * @param array $paramArray
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return Board[] array of Board class
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
        $json = $this->exec($this->uri.'/'.$id.'/issue'.$this->toHttpQueryParameter($paramArray), null);
        $issues = $this->json_mapper->mapArray(
            json_decode($json)->issues, new \ArrayObject(), Issue::class
        );

        return $issues;
    }

    public function getBoardSprints($boardId, $paramArray = [])
    {
        $json = $this->exec($this->uri.'/'.$boardId.'/sprint'.$this->toHttpQueryParameter($paramArray), null);
        $sprints = $this->json_mapper->mapArray(
            json_decode($json)->values,
            new \ArrayObject(),
            Sprint::class
        );

        return $sprints;
    }

    public function getBoardEpics($boardId, $paramArray = [])
    {
        $json = $this->exec($this->uri.'/'.$boardId.'/epic'.$this->toHttpQueryParameter($paramArray), null);
        $epics = $this->json_mapper->mapArray(
            json_decode($json)->values,
            new \ArrayObject(),
            Epic::class
        );

        return $epics;
    }
}
