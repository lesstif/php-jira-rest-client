<?php

require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Timetracking;

class TimetrackingTest extends PHPUnit_Framework_TestCase
{
    private $issueKey = 'TEST-961';

    public function testGetTimetracking()
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->getWorklog($this->issueKey);
            var_dump($ret);
        } catch (JIRAException $e) {
            $this->assertTrue(false, 'testGetTimetracking Failed : '.$e->getMessage());
        }
    }

    public function testPostTimetracking()
    {
        $timeTracking = new Timetracking;

        $timeTracking->setOriginalEstimate('3w 4d 6h');
        $timeTracking->setRemainingEstimate('1w 2d 3h');

        try {
            $issueService = new IssueService();

            $ret = $issueService->worklog($this->issueKey, $timeTracking);
            var_dump($ret);
        } catch (JIRAException $e) {
            $this->assertTrue(false, 'testPostTimetracking Failed : '.$e->getMessage());
        }
    }
}
