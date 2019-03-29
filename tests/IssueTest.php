<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;
use JiraRestApi\JiraException;

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
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testCreateIssue()
    {
        try {
            $issueField = new IssueField();

            $issueField->setProjectKey('TEST')
                        ->setSummary("something's wrong")
                        ->setAssigneeName('lesstif')
                        ->setPriorityName('Critical')
                        ->setIssueType('Bug')
                        ->setDescription('Full description for issue')
                        ->addVersion(['1.0.1', '1.0.3'])
                        ->addComponents(['Component-1', 'Component-2'])
                        ->setDueDate('2019-06-19')
            ;

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);

            //If success, Returns a link to the created issue.
            print_r($ret);

            $issueKey = $ret->{'key'};

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testCreateIssue
     * @param $issueKey
     */
    public function testIssueGet($issueKey)
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->get($issueKey);

            print_r($ret);

            $issueKey = $ret->{'key'};

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testIssueGet
     */
    public function testCreateSubTask($issueKey)
    {
        try {
            $issueField = new IssueField();

            $issueField->setProjectKey('TEST')
                ->setSummary("Subtask - something's wrong")
                ->setAssigneeName('lesstif')
                ->setPriorityName('Critical')
                ->setDescription('Subtask - Full description for issue')
                ->addVersion('1.0.1')
                ->addVersion('1.0.3')
                ->setIssueType('Sub-task')
                ->setParentKeyOrId($issueKey);

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);

            //If success, Returns a link to the created issue.
            print_r($ret);

            $issueKey = $ret->{'key'};

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testCreateIssue
     */
    public function testAddAttachment($issueKey)
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->addAttachments($issueKey,
                ['screen_capture.png', 'bug-description.pdf', 'README.md']);

            print_r($ret);

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Attach Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testAddAttachment
     */
    public function testUpdateIssue($issueKey)
    {
        //$this->markTestIncomplete();
        try {
            $issueField = new IssueField(true);

            $issueField->setAssigneeName('lesstif')
                        ->setPriorityName('Major')
                        ->setIssueType('Task')
                        ->addLabel('test-label-first')
                        ->addLabel('test-label-second')
                        ->addVersion('1.0.1')
                        ->addVersion('1.0.2')
                        ->setDescription('This is a shorthand for a set operation on the summary field');

            $issueService = new IssueService();

            $issueService->update($issueKey, $issueField);

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'update Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testChangeAssignee
     */
    public function testChangeAssignee($issueKey)
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->changeAssignee($issueKey, 'lesstif');

            print_r($ret);

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Change assignee failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testDeleteIssue
     */
    public function testDeleteIssue($issueKey)
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->deleteIssue($issueKey);

            print_r($ret);

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'delete issue failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testUpdateIssue
     */
    public function testAddcomment($issueKey)
    {
        //$this->markTestIncomplete();
        try {
            $comment = new Comment();

            $body = <<<'COMMENT'
Adds a new comment to an issue.
* Bullet 1
* Bullet 2
** sub Bullet 1
** sub Bullet 2
COMMENT;

            $comment->setBody($body)
                ->setVisibility('role', 'Users');

            $issueService = new IssueService();
            $ret = $issueService->addComment($issueKey, $comment);
            print_r($ret);

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'add Comment Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testAddcomment
     */
    public function testTransition($issueKey)
    {
        try {
            $transition = new Transition();
            $transition->setTransitionName('Resolved');
            $transition->setCommentBody('Issue close by REST API.');

            $issueService = new IssueService();
            $ret = $issueService->transition($issueKey, $transition);

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testTransition Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testTransition
     */
    public function testSearch()
    {
        $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        try {
            $issueService = new IssueService();

            $ret = $issueService->search($jql);
            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testSearch
     */
    public function testCustomField()
    {
        $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        try {
            $issueService = new IssueService();

            $ret = $issueService->search($jql);
            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
    }
}
