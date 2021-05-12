<?php

namespace JiraRestApi\RapidCharts;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\GreenHopperTrait;
use Psr\Log\LoggerInterface;

class SprintReportService extends \JiraRestApi\JiraClient
{
    use GreenHopperTrait;

    private $uri = '/rapid/charts/sprintreport';

    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setupAPIUri();
    }

    public function getSprintReportData($rapidViewId, $sprintId, $paramArray = [])
    {
        $paramArray['rapidViewId'] = $rapidViewId;
        $paramArray['sprintId'] = $sprintId;
        $json = $this->exec($this->uri.'/'.$this->toHttpQueryParameter($paramArray), null);
        $sprintReport = json_decode($json);

        return $sprintReport;
    }
}
