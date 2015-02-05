<?php

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;

class IssueTest extends PHPUnit_Framework_TestCase 
{
    public function testIssue()
    {
    	$this->markTestIncomplete();
		try {
			$issueService = new IssueService();

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

			$issueField->setProjectId("12000")
						->setSummary("something's wrong")
						->setAssigneeName("lesstif")
						->setPriorityName("Critical")
						->setIssueType("Bug")
						->setDescription("Full description for issue")
						->addVersion(null, "1.0.1")
						->addVersion(null, "1.0.3");
			
			$issueService = new IssueService();

			$ret = $issueService->create($issueField);

			//If success, Returns a link to the created issue.
			print_r($ret);
		} catch (HTTPException $e) {
			$this->assertTrue(FALSE, "Create Failed : " . $e->getMessage());
		}
	}
	//
}

?>
