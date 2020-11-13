<?php

declare(strict_types=1);

namespace JiraRestApi\Board;

use ArrayObject;
use JiraRestApi\AgileApiTrait;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Epic\Epic;
use JiraRestApi\Exceptions\JiraException;
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
     * @throws JiraException
     *
     * @return \ArrayObject|Board[]|null array of Board class
     */
    public function getBoardList(array $paramArray = []): ?\ArrayObject
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

    /**
     * @param string|int $id
     * @param array      $paramArray
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Board
     */
    public function getBoard($id, array $paramArray = []): Board
    {
        $json = $this->exec($this->uri.'/'.$id.$this->toHttpQueryParameter($paramArray), null);

        return $this->json_mapper->map(
            json_decode($json, false, 512, $this->getJsonOptions()),
            new Board()
        );
    }

    /**
     * @param string|int $id
     * @param array      $paramArray
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return \ArrayObject array of AgileIssue
     */
    public function getBoardIssues($id, array $paramArray = []): ArrayObject
    {
        $json = $this->exec($this->uri.'/'.$id.'/issue'.$this->toHttpQueryParameter($paramArray), null);

        return $this->json_mapper->mapArray(
            json_decode($json, false, 512, $this->getJsonOptions())->issues,
            new \ArrayObject(),
            AgileIssue::class
        );
    }

    /**
     * @param string|int $id
     * @param array      $paramArray
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return \ArrayObject array of AgileIssue
     */
    public function getBoardBacklogIssues($id, array $paramArray = []): \ArrayObject
    {
        $json = $this->exec($this->uri.'/'.$id.'/backlog'.$this->toHttpQueryParameter($paramArray), null);

        return $this->json_mapper->mapArray(
            json_decode($json, false, 512, $this->getJsonOptions())->issues,
            new \ArrayObject(),
            AgileIssue::class
        );
    }

    /**
     * @param string|int $boardId
     * @param array      $paramArray
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return \ArrayObject array of Sprint
     */
    public function getBoardSprints($boardId, array $paramArray = []): \ArrayObject
    {
        $json = $this->exec($this->uri.'/'.$boardId.'/sprint'.$this->toHttpQueryParameter($paramArray), null);

        return $this->json_mapper->mapArray(
            json_decode($json, false, 512, $this->getJsonOptions())->values,
            new \ArrayObject(),
            Sprint::class
        );
    }

    /**
     * @param string|int $boardId
     * @param array      $paramArray
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return \ArrayObject array of Epic
     */
    public function getBoardEpics($boardId, array $paramArray = []): \ArrayObject
    {
        $json = $this->exec($this->uri.'/'.$boardId.'/epic'.$this->toHttpQueryParameter($paramArray), null);

        return $this->json_mapper->mapArray(
            json_decode($json, false, 512, $this->getJsonOptions())->values,
            new \ArrayObject(),
            Epic::class
        );
    }
}
