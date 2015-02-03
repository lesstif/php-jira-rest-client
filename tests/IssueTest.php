<?php

use JiraRestApi\Issue\IssueService;

class IssueTest extends PHPUnit_Framework_TestCase 
{
    public function testIssue()
    {
    	//$this->markTestIncomplete();
		try {
			$issueService = new IssueService(getHostConfig(), getOptions());

			$issue = $issueService->get('TEST-867');
			
			file_put_contents('jira-issue.json', json_encode($issue, JSON_PRETTY_PRINT));

			print_r($issue->fields->assignee);
			/*
			foreach ($issue->components as $c) {
				echo ("COM : " . $c->name . "\n");
			}
			*/
		} catch (HTTPException $e) {
			$this->assertTrue(FALSE, $e->getMessage());
		}
	}

	public function testCreateIssue()
    {
    	$this->markTestIncomplete();
		try {
			$issueService = new IssueService(getHostConfig(), getOptions());

			$issue = new Issue();
			$issue = $issueService->getAllProjects();

			$i = 0;
			foreach ($issue as $p) {
				echo sprintf("Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n",
					$p->key, $p->id, $p->name, $p->projectCategory['name']
					);
					
			}			
		} catch (HTTPException $e) {
			$this->assertTrue(FALSE, $e->getMessage());
		}
	}
	//
}

?>
