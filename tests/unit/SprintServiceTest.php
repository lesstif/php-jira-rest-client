<?php

use JiraRestApi\Sprint\SprintService;
use PHPUnit\Framework\TestCase;

class SprintServiceTest extends TestCase
{
    public function setup()
    {
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

    public function testGetOneSprint()
    {
        $this->response = '{"id":780,"self":"https://test.jira.com/rest/agile/1.0/sprint/780","state":"closed","name":"Sprint 7","startDate":"2018-08-06T11:04:04.515-04:00","endDate":"2018-08-17T18:20:00.000-04:00","completeDate":"2018-08-17T14:02:06.234-04:00","originBoardId":307,"goal":"Do Something Awesome"}';

        $this->restClient->expects($this->once())->method('exec')
          ->will($this->returnValue($this->response));


        $sprintService = new SprintService($this->configurations, $this->logger, './', $this->restClient);
        $sprint = $sprintService->getSprint(780);
        $this->assertEquals(780, $sprint->id);
        $this->assertEquals(307, $sprint->originBoardId);
        $this->assertEquals("Do Something Awesome", $sprint->goal);
    }

    public function testGetEstimatedVelocityForSprint()
    {
        $this->getSprintResponse = '{"id":780,"self":"https://test.jira.com/rest/agile/1.0/sprint/780","state":"closed","name":"Sprint 7","startDate":"2018-08-06T11:04:04.515-04:00","endDate":"2018-08-17T18:20:00.000-04:00","completeDate":"2018-08-17T14:02:06.234-04:00","originBoardId":307,"goal":"Do Something Awesome"}';
        $this->getVelocityResponse = '{"sprints":[{"id":780,"sequence":780,"name":"Sharks Sprint 7","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":779,"sequence":779,"name":"Sharks Sprint 6","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":769,"sequence":769,"name":"Sharks Sprint 5","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":712,"sequence":712,"name":"Sprint 12","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":700,"sequence":700,"name":"Sprint 24","state":"CLOSED","goal":"complete learning how to write ws in RESTng and delivering major ws with tests.","linkedPagesCount":0},{"id":703,"sequence":703,"name":"Sprint 11","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":694,"sequence":694,"name":"Sprint 10","state":"CLOSED","goal":"","linkedPagesCount":0}],"velocityStatEntries":{"769":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":16.0,"text":"16.0"}},"694":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":42.5,"text":"42.5"}},"712":{"estimated":{"value":23.0,"text":"23.0"},"completed":{"value":27.0,"text":"27.0"}},"779":{"estimated":{"value":22.0,"text":"22.0"},"completed":{"value":39.0,"text":"39.0"}},"780":{"estimated":{"value":27.0,"text":"27.0"},"completed":{"value":68.0,"text":"68.0"}},"700":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":0.0,"text":"0.0"}},"703":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":34.0,"text":"34.0"}}}}';


        $this->restClient->expects($this->exactly(2))
                     ->method('exec')
                     ->will($this->onConsecutiveCalls($this->getSprintResponse, $this->getVelocityResponse));


        $sprintService = new SprintService($this->configurations, $this->logger, './', $this->restClient);
        $sprint = $sprintService->getVelocityForSprint(780);
        $this->assertEquals(27, $sprint->estimatedVelocity);
    }

    public function testGetCompletedVelocityForSprint()
    {
        $this->getSprintResponse = '{"id":780,"self":"https://test.jira.com/rest/agile/1.0/sprint/780","state":"closed","name":"Sprint 7","startDate":"2018-08-06T11:04:04.515-04:00","endDate":"2018-08-17T18:20:00.000-04:00","completeDate":"2018-08-17T14:02:06.234-04:00","originBoardId":307,"goal":"Do Something Awesome"}';
        $this->getVelocityResponse = '{"sprints":[{"id":780,"sequence":780,"name":"Sharks Sprint 7","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":779,"sequence":779,"name":"Sharks Sprint 6","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":769,"sequence":769,"name":"Sharks Sprint 5","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":712,"sequence":712,"name":"Sprint 12","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":700,"sequence":700,"name":"Sprint 24","state":"CLOSED","goal":"complete learning how to write ws in RESTng and delivering major ws with tests.","linkedPagesCount":0},{"id":703,"sequence":703,"name":"Sprint 11","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":694,"sequence":694,"name":"Sprint 10","state":"CLOSED","goal":"","linkedPagesCount":0}],"velocityStatEntries":{"769":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":16.0,"text":"16.0"}},"694":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":42.5,"text":"42.5"}},"712":{"estimated":{"value":23.0,"text":"23.0"},"completed":{"value":27.0,"text":"27.0"}},"779":{"estimated":{"value":22.0,"text":"22.0"},"completed":{"value":39.0,"text":"39.0"}},"780":{"estimated":{"value":27.0,"text":"27.0"},"completed":{"value":68.0,"text":"68.0"}},"700":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":0.0,"text":"0.0"}},"703":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":34.0,"text":"34.0"}}}}';


        $this->restClient->expects($this->exactly(2))
                     ->method('exec')
                     ->will($this->onConsecutiveCalls($this->getSprintResponse, $this->getVelocityResponse));


        $sprintService = new SprintService($this->configurations, $this->logger, './', $this->restClient);
        $sprint = $sprintService->getVelocityForSprint(780);
        $this->assertEquals(68, $sprint->completedVelocity);
    }

    public function testNotAbleToGetVelocityForOlderSprint()
    {
        $this->getSprintResponse = '{"id":780,"self":"https://test.jira.com/rest/agile/1.0/sprint/780","state":"closed","name":"Sprint 7","startDate":"2018-08-06T11:04:04.515-04:00","endDate":"2018-08-17T18:20:00.000-04:00","completeDate":"2018-08-17T14:02:06.234-04:00","originBoardId":307,"goal":"Do Something Awesome"}';
        $this->getVelocityResponse = '{"sprints":[{"id":777,"sequence":777,"name":"Sharks Sprint 7","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":779,"sequence":779,"name":"Sharks Sprint 6","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":769,"sequence":769,"name":"Sharks Sprint 5","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":712,"sequence":712,"name":"Sprint 12","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":700,"sequence":700,"name":"Sprint 24","state":"CLOSED","goal":"complete learning how to write ws in RESTng and delivering major ws with tests.","linkedPagesCount":0},{"id":703,"sequence":703,"name":"Sprint 11","state":"CLOSED","goal":"","linkedPagesCount":0},{"id":694,"sequence":694,"name":"Sprint 10","state":"CLOSED","goal":"","linkedPagesCount":0}],"velocityStatEntries":{"769":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":16.0,"text":"16.0"}},"694":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":42.5,"text":"42.5"}},"712":{"estimated":{"value":23.0,"text":"23.0"},"completed":{"value":27.0,"text":"27.0"}},"779":{"estimated":{"value":22.0,"text":"22.0"},"completed":{"value":39.0,"text":"39.0"}},"777":{"estimated":{"value":27.0,"text":"27.0"},"completed":{"value":68.0,"text":"68.0"}},"700":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":0.0,"text":"0.0"}},"703":{"estimated":{"value":0.0,"text":"0.0"},"completed":{"value":34.0,"text":"34.0"}}}}';


        $this->restClient->expects($this->exactly(2))
                     ->method('exec')
                     ->will($this->onConsecutiveCalls($this->getSprintResponse, $this->getVelocityResponse));


        $sprintService = new SprintService($this->configurations, $this->logger, './', $this->restClient);
        $sprint = $sprintService->getVelocityForSprint(780);
        $this->assertEquals(null, $sprint->completedVelocity);
    }
    //TODO add Test for sprint with no originBoardID
}
