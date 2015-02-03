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
			$issueField->setProjectName("TEST");
			$issueField->setSummary("something's wrong");
			//$issueField->setReporterName("smithers");
			$issueField->setAssigneeName("lesstif");
			$issueField->setPriorityName("Critical");
			$issueField->setIssueType("Bug");
			$issueField->setDescription("Full description for issue");

			$issueField->addVersion(null, "1.0.1");
			$issueField->addVersion(null, "1.0.3");
			
			$issueService = new IssueService(getHostConfig(), getOptions());

			$ret = $issueService->create($issueField);

			print_r($ret);
		} catch (HTTPException $e) {
			$this->assertTrue(FALSE, "Create Failed : " . $e->getMessage());
		}
	}
	//
}

?>
