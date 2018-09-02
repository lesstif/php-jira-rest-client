<?php

use JiraRestApi\Board\BoardService;
use PHPUnit\Framework\TestCase;

class BoardServiceTest extends TestCase {

  public function setup(){

    $this->restClient = $this->getMockBuilder(JiraRestApi\JiraClient::class)
                    ->disableOriginalConstructor()
                    ->setMethods(array('exec','loadConfigs','setLogger','json_mapper'))
                    ->getMock();

    $this->sprint = new \JiraRestApi\Board\Board;

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

  public function testCanGetOneBoard() {
    $this->response = '{"id":3,"self":"https://test.jira.com/rest/agile/1.0/board/3","name":"ABC","type":"scrum"}';

    $this->restClient->method('exec')
          ->willReturn($this->response);

    $boardService = new BoardService($this->configurations,$this->logger,'./',$this->restClient);
    $board = $boardService->getBoard(3);
    $this->assertEquals(3,$board->id);
    $this->assertEquals('ABC',$board->name);
    $this->assertEquals('scrum',$board->type);
  }
  public function testCanGetMultipleBoardsFromOneBoardCall() {
    $this->response = '{"maxResults":50,"startAt":0,"isLast":true,"values":[{"id":3,"self":"https://test.jira.com/rest/agile/1.0/board/3","name":"ABC","type":"scrum"},{"id":5,"self":"https://test.jira.com/rest/agile/1.0/board/5","name":"XYZ","type":"scrum"}]}';

    $this->restClient->expects($this->once())
          ->method('exec')
          ->willReturn($this->response);

    $boardService = new BoardService($this->configurations,$this->logger,'./',$this->restClient);
    $boardList = $boardService->getAllBoards();
    $this->assertEquals(2,count($boardList));
    $this->assertEquals(3,$boardList[0]->id);
    $this->assertEquals('ABC',$boardList[0]->name);
    $this->assertEquals('scrum',$boardList[0]->type);
    $this->assertEquals(5,$boardList[1]->id);
    $this->assertEquals('XYZ',$boardList[1]->name);
    $this->assertEquals('scrum',$boardList[1]->type);
  }
  public function testCanGetMultipleBoardsFromMultipleBoardCall() {
    $this->response = '{"maxResults":2,"startAt":0,"isLast":false,"values":[{"id":3,"self":"https://test.jira.com/rest/agile/1.0/board/3","name":"ABC","type":"scrum"},{"id":5,"self":"https://test.jira.com/rest/agile/1.0/board/5","name":"XYZ","type":"scrum"}]}';
    $this->response2 = '{"maxResults":2,"startAt":0,"isLast":true,"values":[{"id":7,"self":"https://test.jira.com/rest/agile/1.0/board/7","name":"QRS","type":"scrum"},{"id":9,"self":"https://test.jira.com/rest/agile/1.0/board/9","name":"NOP","type":"scrum"}]}';

    $this->restClient->expects($this->exactly(2))
                     ->method('exec')
                     ->will($this->onConsecutiveCalls($this->response,$this->response2));

    $boardService = new BoardService($this->configurations,$this->logger,'./',$this->restClient);
    $boardList = $boardService->getAllBoards();
    $this->assertEquals(4,count($boardList));
    $this->assertEquals(3,$boardList[0]->id);
    $this->assertEquals('ABC',$boardList[0]->name);
    $this->assertEquals('scrum',$boardList[0]->type);
    $this->assertEquals(5,$boardList[1]->id);
    $this->assertEquals('XYZ',$boardList[1]->name);
    $this->assertEquals('scrum',$boardList[1]->type);
    $this->assertEquals(7,$boardList[2]->id);
    $this->assertEquals(9,$boardList[3]->id);

  }

  public function testGetSprintsAssociatedWithOneBoard(){
    $this->response = '{"id":3,"self":"https://test.jira.com/rest/agile/1.0/board/3","name":"ABC","type":"scrum"}';
    $this->sprintResponse = '{"maxResults":50,"startAt":0,"isLast":true,"values":[{"id":3,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/3","state":"closed","name":"Sprint6","startDate":"2015-02-15T13:19:32.367-05:00","endDate":"2015-03-06T13:19:00.000-05:00","completeDate":"2015-03-06T09:34:05.398-05:00","originBoardId":3},{"id":4,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/4","state":"closed","name":"Sprint7","startDate":"2015-03-06T09:34:32.226-05:00","endDate":"2015-03-20T09:34:00.000-04:00","completeDate":"2015-04-27T08:54:33.787-04:00","originBoardId":3},{"id":22,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/22","state":"closed","name":"Sprint9","startDate":"2015-04-27T08:55:07.705-04:00","endDate":"2015-05-14T08:55:00.000-04:00","completeDate":"2015-05-18T09:39:08.551-04:00","originBoardId":3},{"id":25,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/25","state":"closed","name":"Sprint9","startDate":"2015-04-27T08:55:07.705-04:00","endDate":"2015-05-14T08:55:00.000-04:00","completeDate":"2015-05-18T09:39:08.551-04:00","originBoardId":3}]}';
    $this->restClient->expects($this->exactly(2))
          ->method('exec')
          ->willReturn($this->response,$this->sprintResponse);

    $boardService = new BoardService($this->configurations,$this->logger,'./',$this->restClient);
    $boards = $boardService->getBoardWithSprintsList(3);
    $this->assertEquals(3,$boards[0]->id);
    $this->assertEquals(4,count($boards[0]->sprintList));
  }

  public function testGetSprintsAssociatedWithAllBoard(){
    $this->response = '{"maxResults":50,"startAt":0,"isLast":true,"values":[{"id":3,"self":"https://test.jira.com/rest/agile/1.0/board/3","name":"ABC","type":"scrum"},{"id":5,"self":"https://test.jira.com/rest/agile/1.0/board/5","name":"XYZ","type":"scrum"}]}';
    $this->boardResponse = '{"id":3,"self":"https://test.jira.com/rest/agile/1.0/board/3","name":"ABC","type":"scrum"}';
    $this->sprintResponse = '{"maxResults":50,"startAt":0,"isLast":true,"values":[{"id":3,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/3","state":"closed","name":"Sprint6","startDate":"2015-02-15T13:19:32.367-05:00","endDate":"2015-03-06T13:19:00.000-05:00","completeDate":"2015-03-06T09:34:05.398-05:00","originBoardId":3},{"id":4,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/4","state":"closed","name":"Sprint7","startDate":"2015-03-06T09:34:32.226-05:00","endDate":"2015-03-20T09:34:00.000-04:00","completeDate":"2015-04-27T08:54:33.787-04:00","originBoardId":3},{"id":22,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/22","state":"closed","name":"Sprint9","startDate":"2015-04-27T08:55:07.705-04:00","endDate":"2015-05-14T08:55:00.000-04:00","completeDate":"2015-05-18T09:39:08.551-04:00","originBoardId":3}]}';
    $this->boardResponse2 = '{"id":5,"self":"https://test.jira.com/rest/agile/1.0/board/5","name":"XYZ","type":"scrum"}';
    $this->sprintResponse2 = '{"maxResults":50,"startAt":0,"isLast":true,"values":[{"id":15,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/15","state":"closed","name":"Sprint16","startDate":"2015-02-15T13:19:32.367-05:00","endDate":"2015-03-06T13:19:00.000-05:00","completeDate":"2015-03-06T09:34:05.398-05:00","originBoardId":5},{"id":41,"self":"https://jira.miamioh.edu/rest/agile/1.0/sprint/41","state":"closed","name":"Sprint 27","startDate":"2015-03-06T09:34:32.226-05:00","endDate":"2015-03-20T09:34:00.000-04:00","completeDate":"2015-04-27T08:54:33.787-04:00","originBoardId":5}]}';
    $this->restClient->expects($this->exactly(5))
          ->method('exec')
          ->willReturn($this->response,$this->boardResponse,$this->sprintResponse,$this->boardResponse2,$this->sprintResponse2);

    $boardService = new BoardService($this->configurations,$this->logger,'./',$this->restClient);
    $boards = $boardService->getBoardWithSprintsList();
    $this->assertEquals(3,$boards[0]->id);
    $this->assertEquals(3,count($boards[0]->sprintList));
  }


}
