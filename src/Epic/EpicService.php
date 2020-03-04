<?php

namespace JiraRestApi\Epic;

use JiraRestApi\AgileApiTrait;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Issue\AgileIssue;
use Psr\Log\LoggerInterface;

class EpicService extends \JiraRestApi\JiraClient
{
    use AgileApiTrait;

    private $uri = '/epic';

    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setupAPIUri();
    }

    public function getEpic($id, $paramArray = []): ?Epic
    {
        $response = $this->exec($this->uri.'/'.$id.$this->toHttpQueryParameter($paramArray), null);

        try {
            return $this->json_mapper->map(
                json_decode($response, false, 512, JSON_THROW_ON_ERROR),
                new Epic()
            );
        } catch (\JsonException $exception) {
            $this->log->error("Response cannot be decoded from json\nException: {$exception->getMessage()}");

            return null;
        }
    }

    /**
     * @return \ArrayObject|AgileIssue[]|null
     */
    public function getEpicIssues($id, $paramArray = []): ?\ArrayObject
    {
        $response = $this->exec($this->uri.'/'.$id.'/issue'.$this->toHttpQueryParameter($paramArray), null);

        try {
            return $this->json_mapper->mapArray(
                json_decode($response, false, 512, JSON_THROW_ON_ERROR)->issues,
                new \ArrayObject(),
                AgileIssue::class
            );
        } catch (\JsonException $exception) {
            $this->log->error("Response cannot be decoded from json\nException: {$exception->getMessage()}");

            return null;
        }
    }
}
