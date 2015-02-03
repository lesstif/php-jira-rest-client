<?php

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;

class IssueTest extends PHPUnit_Framework_TestCase 
{
    public function testIssue()
    {
    	$this->markTestIncomplete();
		try {
			$issueService = new IssueService(getHostConfig(), getOptions());

			$issue = $issueService->get('TEST-867');
			
			file_put_contents('jira-issue.json', json_encode($issue, JSON_PRETTY_PRINT));

			print_r($issue->fields->versions[0]);

			//foreach ($issue->fields->comment->comments as $c) {
			//	echo ("comment : " . $c->body . "\n");
			//}
			
		} catch (HTTPException $e) {
			$this->assertTrue(FALSE, $e->getMessage());
		}
	}

	public function testCreateIssue()
    {
    	//$this->markTestIncomplete();
		try {
			$issueField = new IssueField();
			$issueField->project->name = "TEST";
			$issueField->summary = "something's wrong";
			$issueField->reporter->name = "smithers";
			$issueField->assignee->name = "homer";
			$issueField->priority->name = "Critical";
			$issueField->description = "Full description for issue";

			//$issueService = new IssueService(getHostConfig(), getOptions());

			//$ret = $issueService->create($issueField);

			//print_r($ret);
		} catch (HTTPException $e) {
			$this->assertTrue(FALSE, "Create Failed : " . $e->getMessage());
		}
	}
	//
}

?>
