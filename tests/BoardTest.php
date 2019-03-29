<?php

use JiraRestApi\Board\BoardService;
use JiraRestApi\Board\Board;
use JiraRestApi\Board\Location;
use JiraRestApi\Issue\Issue;

/**
 * Test agile boards integration.
 */
class BoardTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test we can obtain the board list.
     */
    public function testGetBoards()
    {
        $board_service = new BoardService();

        $board_list = $board_service->getBoardList();
        $this->assertInstanceOf(ArrayObject::class, $board_list, 'We receive a board list.');
        /** @var \JiraRestApi\Board\Board $first_board */
        $first_board = reset($board_list);
        $this->assertInstanceOf(Board::class, $first_board, 'Each element of the list is a Board instance.');

    }

    /**
     * Test we can obtain a single board.
     */
    public function testGetBoard()
    {
        $board_service = new BoardService();

        $board = $board_service->getBoard(42);

        /** @var \JiraRestApi\Board\Board $board */
        $this->assertInstanceOf(Board::class, $board, 'We receive a board instance');
        $this->assertNotEmpty($board->getId(), 'Check board id.');
        $this->assertNotEmpty($board->getName(), 'Check board name.');
        $this->assertNotEmpty($board->getType(), 'Check board type.');
        $this->assertNotEmpty($board->getSelf(), 'Check board self.');
        $this->assertInstanceOf(Location::class, $board->getLocation(), 'Check board location.');

    }

    /**
     * Test we can obtain board issues.
     */
    public function testGetBoardIssues()
    {
        $board_service = new BoardService();
        $board_issues = $board_service->getBoardIssues(42);
        $this->assertInstanceOf(ArrayObject::class, $board_issues, 'We receive a board issue list.');
        $first_issue = reset($board_issues);
        $this->assertInstanceOf(Issue::class, $first_issue);
        $this->assertNotEmpty($first_issue->id);
    }

}
