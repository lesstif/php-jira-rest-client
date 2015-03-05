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
    	$this->markTestIncomplete();
		try {
			$issueField = new IssueField();

			$issueField->setProjectKey("TEST")
						->setSummary("something's wrong")
						->setAssigneeName("lesstif")
						->setPriorityName("Critical")
						->setIssueType("Bug")
						->setDescription("Full description for issue")
						->addVersion("1.0.1")
						->addVersion("1.0.3")
						;
			
			$issueService = new IssueService();

			$ret = $issueService->create($issueField);

			//If success, Returns a link to the created issue.
			print_r($ret);

			$issueKey = $ret->{'key'};
			return $issueKey;
		} catch (JIRAException $e) {
			$this->assertTrue(FALSE, "Create Failed : " . $e->getMessage());
		}
	}
	//

	/**
     * @depends testCreateIssue
     * 
     */
	public function testAddAttachment($issueKey)
    {
    	$this->markTestIncomplete();
		try {
			
			$issueService = new IssueService();

			$ret = $issueService->addAttachments($issueKey, 'screen_capture.png');

			print_r($ret);

			return $issueKey;
		} catch (JIRAException $e) {
			$this->assertTrue(FALSE, "Attach Failed : " . $e->getMessage());
		}
	}

	/**
     * depends testAddAttachment
     * 
     */
	public function testUpdateIssue()
    {
    	$issueKey = "TEST-920";

    	//$this->markTestIncomplete();
		try {			
			$issueField = new IssueField(true);

			$issueField->setAssigneeName("admin")
						->setPriorityName("Major")
						->setIssueType("Task")
						->addLabel("test-label-first")
						->addLabel("test-label-second")
						->addVersion("1.0.1")
						->addVersion("1.0.2")
						->setDescription("This is a shorthand for a set operation on the summary field")
						;

			$issueService = new IssueService();

			$issueService->update($issueKey, $issueField);
		} catch (JIRAException $e) {
			$this->assertTrue(FALSE, "update Failed : " . $e->getMessage());
		}
	}

}

?>
