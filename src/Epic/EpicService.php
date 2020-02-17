<?php

namespace JiraRestApi\Epic;

use JiraRestApi\AgileApiTrait;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Issue\Issue;
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

    public function getEpic($id, $paramArray = [])
    {
        $json = $this->exec($this->uri.'/'.$id.$this->toHttpQueryParameter($paramArray), null);
        $epic = $this->json_mapper->map(json_decode($json), new Epic());

        return $epic;
    }

    public function getEpicIssues($id, $paramArray = [])
    {
        $json = $this->exec($this->uri.'/'.$id.'/issue'.$this->toHttpQueryParameter($paramArray), null);
        $issues = $this->json_mapper->mapArray(json_decode($json)->issues, new \ArrayObject(), Issue::class);

        return $issues;
    }
}
