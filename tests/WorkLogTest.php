<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraException;

class WorkLogTest extends PHPUnit_Framework_TestCase
{
    public $issueKey = 'TEST-165';

    public function testGetWorkLog()
    {
        try {
            $issueService = new IssueService();

            // get issue's worklog
            $pwl = $issueService->getWorklog($this->issueKey);
            $worklogs = $pwl->getWorklogs();

            Dumper::dump($worklogs);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testGetWorkLog Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testGetWorkLog
     */
    public function testAddWorkLogInIssue()
    {
        try {
            $workLog = new Worklog();

            $workLog->setComment('I did some work here.')
                ->setStarted('2016-05-28 12:35:54')
                ->setTimeSpent('1d 2h 3m');

            $issueService = new IssueService();

            $ret = $issueService->addWorklog($this->issueKey, $workLog);

            Dumper::dump($ret);

            $workLogid = $ret->{'id'};

            return $workLogid;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testAddWorkLogInIssue
     */
    public function testEditWorkLogInIssue($workLogid)
    {
        try {
            $workLog = new Worklog();

            $workLog->setComment('I did edit previous worklog here.')
                ->setStarted('2016-05-29 13:41:12')
                ->setTimeSpent('2d 7h 5m');

            $issueService = new IssueService();

            $ret = $issueService->editWorklog($this->issueKey, $workLog, $workLogid);

            Dumper::dump($ret);

            $workLogid = $ret->{'id'};

            return $workLogid;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testUpdateWorkLogInIssue
     */
    public function testGetWorkLogById($workLogid)
    {
        try {
            $issueService = new IssueService();

            $worklog = $issueService->getWorklogById($this->issueKey, $workLogid);

            Dumper::dump($worklog);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testGetWorkLogById Failed : '.$e->getMessage());
        }
    }
}
