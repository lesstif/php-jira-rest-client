<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

class IssueTest extends PHPUnit_Framework_TestCase
{
    public function testSearch()
    {
        $this->markTestSkipped();

        $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        try {
            $issueService = new IssueService();

            $ret = $issueService->search($jql);

            foreach ($ret as $issue) {
                Dumper::dump($issue);
            }
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
    }

    public function testGetIssue()
    {
        $issueKey = 'TEST-155';
        try {
            $issueService = new IssueService();

            $queryParam = [
                'fields' => [  // default: '*all'
                    'summary',
                    'comment',
                ],
                'expand' => [
                    'renderedFields',
                    'names',
                    'schema',
                    'transitions',
                    'operations',
                    'editmeta',
                    'changelog',
                ],
            ];

            $issue = $issueService->get($issueKey, $queryParam);

            Dumper::dump($issue);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
    }
}
