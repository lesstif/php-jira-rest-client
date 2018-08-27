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

    $this->restClient->method('exec')
          ->willReturn($this->response);

    $boardService = new BoardService($this->configurations,$this->logger,'./',$this->restClient);
    $boardList = $boardService->getAllBoards();
    $this->assertEquals(3,$boardList[0]->id);
    $this->assertEquals('ABC',$boardList[0]->name);
    $this->assertEquals('scrum',$boardList[0]->type);
    $this->assertEquals(5,$boardList[1]->id);
    $this->assertEquals('XYZ',$boardList[1]->name);
    $this->assertEquals('scrum',$boardList[1]->type);
  }

//TODO Add Test to on getting AllBoards when more then one call to exec is needed.


}
