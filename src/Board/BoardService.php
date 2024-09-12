<?php

namespace JiraRestApi\Board;

use JiraRestApi\AgileApiTrait;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Epic\Epic;
use JiraRestApi\Issue\AgileIssue;
use JiraRestApi\Sprint\Sprint;
use Psr\Log\LoggerInterface;

class BoardService extends \JiraRestApi\JiraClient
{
    use AgileApiTrait;

    private $uri = '/board';

    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setupAPIUri();
    }

    /**
     * get all boards list.
     *
     * @param array $paramArray
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return \ArrayObject|Board[]|null array of Board class
     */
    public function getBoardList($paramArray = []): ?\ArrayObject
    {
        $json = $this->exec($this->uri.$this->toHttpQueryParameter($paramArray), null);

        try {
            return $this->json_mapper->mapArray(
                json_decode($json, false, 512, $this->getJsonOptions())->values,
                new \ArrayObject(),
                Board::class
            );
        } catch (\JsonException $exception) {
            $this->log->error("Response cannot be decoded from json\nException: {$exception->getMessage()}");

            return null;
        }
    }

    public function getBoard($id, $paramArray = []): ?Board
    {
        $json = $this->exec($this->uri.'/'.$id.$this->toHttpQueryParameter($paramArray), null);

        try {
            return $this->json_mapper->map(
                json_decode($json, false, 512, $this->getJsonOptions()),
                new Board()
            );
        } catch (\JsonException $exception) {
            $this->log->error("Response cannot be decoded from json\nException: {$exception->getMessage()}");

            return null;
        }
    }

    /**
     * @return \ArrayObject|AgileIssue[]|null
     */
    public function getBoardIssues($id, $paramArray = []): ?\ArrayObject
    {
        $json = $this->exec($this->uri.'/'.$id.'/issue'.$this->toHttpQueryParameter($paramArray), null);

        try {
            return $this->json_mapper->mapArray(
                json_decode($json, false, 512, $this->getJsonOptions())->issues,
                new \ArrayObject(),
                AgileIssue::class
            );
        } catch (\JsonException $exception) {
            $this->log->error("Response cannot be decoded from json\nException: {$exception->getMessage()}");

            return null;
        }
    }

    /**
     * @return \ArrayObject|AgileIssue[]|null
     */
    public function getBoardBacklogIssues($id, array $paramArray = []): ?\ArrayObject
    {
        $json = $this->exec($this->uri.'/'.$id.'/backlog'.$this->toHttpQueryParameter($paramArray), null);

        try {
            return $this->json_mapper->mapArray(
                json_decode($json, false, 512, $this->getJsonOptions())->issues,
                new \ArrayObject(),
                AgileIssue::class
            );
        } catch (\JsonException $exception) {
            $this->log->error("Response cannot be decoded from json\nException: {$exception->getMessage()}");

            return null;
        }
    }

    /**
     * @return \ArrayObject|Sprint[]|null
     */
    public function getBoardSprints($boardId, $paramArray = []): ?\ArrayObject
    {
        $json = $this->exec($this->uri.'/'.$boardId.'/sprint'.$this->toHttpQueryParameter($paramArray), null);

        try {
            return $this->json_mapper->mapArray(
                json_decode($json, false, 512, $this->getJsonOptions())->values,
                new \ArrayObject(),
                Sprint::class
            );
        } catch (\JsonException $exception) {
            $this->log->error("Response cannot be decoded from json\nException: {$exception->getMessage()}");

            return null;
        }
    }

    /**
     * @return \ArrayObject|Epic[]|null
     */
    public function getBoardEpics($boardId, $paramArray = []): ?\ArrayObject
    {
        $json = $this->exec($this->uri.'/'.$boardId.'/epic'.$this->toHttpQueryParameter($paramArray), null);

        try {
            return $this->json_mapper->mapArray(
                json_decode($json, false, 512, $this->getJsonOptions())->values,
                new \ArrayObject(),
                Epic::class
            );
        } catch (\JsonException $exception) {
            $this->log->error("Response cannot be decoded from json\nException: {$exception->getMessage()}");

            return null;
        }
    }
}
