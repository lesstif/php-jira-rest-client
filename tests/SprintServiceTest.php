<?php

use JiraRestApi\Sprint\SprintService;
use PHPUnit\Framework\TestCase;

class SprintServiceTest extends TestCase {

  public function setup(){

    $this->restClient = $this->getMockBuilder(JiraRestApi\JiraClient::class)
                    ->disableOriginalConstructor()
                    ->setMethods(array('exec','loadConfigs','setLogger','json_mapper'))
                    ->getMock();

    $this->sprint = new \JiraRestApi\Sprint\Sprint;

    $this->jsonMapper = $this->getMockBuilder(\JsonMapper::class)
          ->setMethods(array('map'))
          ->getMock();

    $this->jsonMapper->method('map')
          ->willReturn($this->sprint);


    $this->restClient->method('json_mapper')
          ->willReturn($this->jsonMapper);

    $this->configurations = $this->getMockBuilder(JiraRestApi\Configuration\ConfigurationInterface::class)
          ->getMock();

    $this->configurations->method('getJiraLogLevel')
          ->willReturn('DEBUG');

    $this->restClient->method('setLogger')
          ->willReturn('');

    $this->restClient->method('loadConfigs')
          ->willReturn($this->configurations);

    $this->logger = $this->getMockBuilder(Monolog\Logger::class)
          ->disableOriginalConstructor()
          ->getMock();
  }

  public function testGetOneSprint() {
    $this->response = '{"id":780,"self":"https://test.jira.com/rest/agile/1.0/sprint/780","state":"closed","name":"Sprint 7","startDate":"2018-08-06T11:04:04.515-04:00","endDate":"2018-08-17T18:20:00.000-04:00","completeDate":"2018-08-17T14:02:06.234-04:00","originBoardId":307,"goal":""}';

    $this->restClient->method('exec')
          ->willReturn($this->response);


    $sprintService = new SprintService($this->configurations,$this->logger,'./',$this->restClient);
    $sprint = $sprintService->getSprint(780);
    $this->assertEquals(780,$sprint->id);
  }



}
