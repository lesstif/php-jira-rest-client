<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Watcher;
use JiraRestApi\JiraException;

class WatcherLogTest extends PHPUnit_Framework_TestCase
{
    public $issueKey = 'TEST-165';

    public function testAddWatcherLog()
    {
        try {
            $issueService = new IssueService();

            $watcher = new Watcher('lesstif');
            // add issue watcher
            $ret = $issueService->addWatcher($this->issueKey, $watcher);

            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testAddWatcherLog Failed : '.$e->getMessage());
        }
    }
}
