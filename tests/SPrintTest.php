<?php
namespace JiraRestApi\Test;

use DateInterval;
use DateTime;
use Exception;
use JiraRestApi\JiraException;
use JiraRestApi\Sprint\Sprint;
use JiraRestApi\Sprint\SprintService;
use PHPUnit\Framework\TestCase;
use JiraRestApi\Dumper;
use JiraRestApi\Issue\Reporter;

class SPrintTest extends TestCase
{
    /**
     * @test
     *
     */
    public function create_sprint() : int
    {
        $start = (new DateTime('NOW'))->add(DateInterval::createFromDateString('1 month 5 day'));

        $sp = (new Sprint())
            ->setNameAsString("My Sprint 1")
            ->setGoalAsString("goal")
            ->setOriginBoardIdAsStringOrInt(3)
            ->setStartDateAsDateTime($start)
            ->setEndDateAsDateTime($start->add(DateInterval::createFromDateString('3 week')))
        ;

        try {
            $sps = new SprintService();

            $sprint = $sps->createSprint($sp);

            $this->assertNotNull($sprint->name);

            return $sprint->id;

        } catch (Exception $e) {
            $this->fail('testSearch Failed : '.$e->getMessage());
        }
    }

    /**
     * @test
     *
     * @depends create_sprint
     *
     * @throws \JsonMapper_Exception
     */
    public function get_sprints(int $sprintId) : int
    {
        try {
            $sps = new SprintService();

            $sprint = $sps->getSprint($sprintId);

            $this->assertNotNull($sprint->name);
            Dumper::dump($sprint);

            return $sprintId;
        } catch (Exception $e) {
            $this->fail('testSearch Failed : '.$e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_sprints
     *
     * @param int $sprintId
     * @return int
     */
    public function get_issues_in_sprints(int $sprintId) : int
    {
        try {
            $sps = new SprintService();

            $sprint = $sps->getSprintIssues($sprintId);

            $this->assertNotNull($sprint);
            Dumper::dump($sprint);

            return $sprintId;
        } catch (Exception $e) {
            $this->fail('testSearch Failed : '.$e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_issues_in_sprints
     *
     * @param int $sprintId
     * @return int
     */
    public function move_issues_to_sprints(int $sprintId) : int
    {
        try {
            $sp = (new Sprint())
                ->setMoveIssues([
                    "MOBL-1",
                    "MOBL-5",
                ])

            ;

            $sps = new SprintService();

            $sprint = $sps->moveIssues2Sprint($sprintId, $sp);

            $this->assertNotNull($sprint);

            return $sprintId;
        } catch (Exception $e) {
            $this->fail('move_issues_to_sprints Failed : '.$e->getMessage());
        }
    }
}