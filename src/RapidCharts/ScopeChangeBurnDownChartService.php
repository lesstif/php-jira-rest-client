<?php

namespace JiraRestApi\RapidCharts;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\GreenHopperTrait;
use Psr\Log\LoggerInterface;

class ScopeChangeBurnDownChartService extends \JiraRestApi\JiraClient
{
    use GreenHopperTrait;

    private $uri = '/rapid/charts/scopechangeburndownchart';

    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setupAPIUri();
    }

    public function getBurnDownChartData($rapidViewId, $sprintId, $paramArray = [])
    {
        $paramArray['rapidViewId'] = $rapidViewId;
        $paramArray['sprintId'] = $sprintId;
        $json = $this->exec($this->uri.'/'.$this->toHttpQueryParameter($paramArray), null);
        $burnDownChart = json_decode($json);

        return $burnDownChart;
    }
}
