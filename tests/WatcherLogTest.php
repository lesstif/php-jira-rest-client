<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\JiraException;
use PHPUnit\Framework\TestCase;

class WatcherLogTest extends TestCase
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
}
