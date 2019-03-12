<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\JiraException;

class WatcherLogTest extends PHPUnit_Framework_TestCase
{
    public $issueKey = 'TEST-315';

    public function testAddWatcherLog()
    {
        try {
            $issueService = new IssueService();

            // add issue watcher
            $ret = $issueService->addWatcher($this->issueKey, 'lesstif');

            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testAddWatcherLog Failed : '.$e->getMessage());
        }
    }

    public function testRemoveWatcherLog()
    {
        try {
            $issueService = new IssueService();

            // remove issue watcher
            $ret = $issueService->removeWatcher($this->issueKey, 'lesstif');

            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testRemoveWatcherLog Failed : '.$e->getMessage());
        }
    }
}
