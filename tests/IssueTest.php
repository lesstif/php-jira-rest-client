<?php

namespace JiraRestApi\Test;

use DateInterval;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use JiraRestApi\Dumper;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;
use JiraRestApi\JiraException;

class IssueTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function testIssue()
    {
        $this->markTestIncomplete();
        try {
            $issueService = new IssueService();

            $issue = $issueService->get('TEST-702');

            file_put_contents('jira-issue.json', json_encode($issue, JSON_PRETTY_PRINT));

            print_r($issue->fields->versions[0]);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @test
     * @return string
     */
    public function create_issue() : string
    {
        try {
            $issueField = new IssueField();

            $due = (new DateTime('NOW'))->add(DateInterval::createFromDateString('1 month 5 day'));

            $issueField->setProjectKey('TEST')
                        ->setSummary("something's wrong")
                        ->setAssigneeNameAsString('lesstif')
                        ->setPriorityNameAsString('Critical')
                        ->setIssueTypeAsString('Bug')
                        ->setDescription('Full description for issue')
                        ->addVersionAsArray(['1.0.1', '1.0.3'])
                        //->addComponentsAsArray(['Component-1', 'Component-2'])
                        ->addComponentAsString('Component-1')
                        ->setDueDateAsDateTime(
                            (new DateTime('NOW'))->add(DateInterval::createFromDateString('1 month 5 day'))
                        )
                        //->setDueDateAsString('2022-10-03')
            ;

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);

            //If success, Returns a link to the created issue.
            print_r($ret);

            $issueKey = $ret->{'key'};

            $this->assertNotNull($issueKey);

            return $issueKey;
        } catch (Exception $e) {
            $this->fail('Create Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends create_issue
     */
    public function get_created_issue(string $issueKey) : string
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->get($issueKey);

            //print_r($ret);
            $issueKey = $ret->{'key'};

            $this->assertNotNull($issueKey);
            $this->assertNotNull($ret->fields->summary);
            $this->assertNotNull($ret->fields->issuetype);

            return $issueKey;
        } catch (Exception $e) {
            $this->fail('Create Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_created_issue
     */
    public function update_issue(string $subTaskIssueKey) :string
    {
        //$this->markTestIncomplete();
        try {
            $issueField = new IssueField(true);

            $issueField->setAssigneeNameAsString('lesstif')
                ->setPriorityNameAsString('Major')
                ->setIssueTypeAsString('Task')
                ->addLabelAsString('test-label-first')
                ->addLabelAsString('test-label-second')
                ->addVersionAsString('1.0.1')
                ->addVersionAsArray(['1.0.2'])
                ->setDescription('This is a shorthand for a set operation on the summary field');

            $issueService = new IssueService();

            $issueService->update($subTaskIssueKey, $issueField);

            $this->assertNotNull($subTaskIssueKey);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('update Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends update_issue
     */
    public function create_subTask_issue(string $issueKey) :string
    {
        try {
            $issueField = new IssueField();

            $issueField->setProjectKey('TEST')
                ->setSummary("Subtask - something's wrong")
                ->setAssigneeNameAsString('lesstif')
                ->setPriorityNameAsString('Critical')
                ->setDescription('Subtask - Full description for issue')
                ->addVersionAsString('1.0.1')
                ->addVersionAsString('1.0.3')
                ->setIssueTypeAsString('Sub-task')
                ->setParentKeyOrId($issueKey);

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);

            $subTaskIssueKey = $ret->{'key'};

            $this->assertNotNull($subTaskIssueKey);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('Create Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends create_subTask_issue
     */
    public function get_created_subtask_issue(string $subTaskIssueKey) : string
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->get($subTaskIssueKey);

            $issueKey = $ret->{'key'};

            $this->assertNotNull($issueKey);
            $this->assertNotNull($ret->fields->summary);
            //$this->assertEquals('Sub-task', $ret->fields->issuetype->name);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('Create Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_created_subtask_issue
     */
    public function add_attachment_on_subtask_issue(string $subTaskIssueKey) :string
    {
        try {
            $files = [
                'screen_capture_스크린-캡춰.png',
                'bug-description.pdf',
                'README.md',
            ];

            $issueService = new IssueService();

            // $ret is Array of JiraRestApi\Issue\Attachment
            $ret = $issueService->addAttachments($subTaskIssueKey, $files);

            $this->assertNotNull($subTaskIssueKey);
            $this->assertSameSize($files, $ret);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('Attach Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends add_attachment_on_subtask_issue
     */
    public function change_assignee(string $subTaskIssueKey) :string
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->changeAssignee($subTaskIssueKey, 'lesstif');

            print_r($ret);

            $this->assertNotNull($subTaskIssueKey);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('Change assignee failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends change_assignee
     */
    public function delete_issue(string $subTaskIssueKey) :string
    {
        $this->markTestSkipped();

        try {
            $issueService = new IssueService();

            $ret = $issueService->deleteIssue($subTaskIssueKey);

            print_r($ret);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('delete issue failed : '.$e->getMessage());
        }
    }

    /**
     * @test
     * @depends change_assignee
     */
    public function add_comments(string $subTaskIssueKey) :string
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
                ->setVisibilityAsString('role', 'Users');

            $issueService = new IssueService();
            $ret = $issueService->addComment($subTaskIssueKey, $comment);
            print_r($ret);
            $this->assertNotNull($ret);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('add Comment Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends add_comments
     */
    public function testTransition(string $subTaskIssueKey) : string
    {
        try {
            $transition = new Transition();
            $transition->setTransitionName('Resolved');
            $transition->setCommentBody('Issue close by REST API.');

            $issueService = new IssueService();
            $ret = $issueService->transition($subTaskIssueKey, $transition);

            $this->assertNotNull($ret);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('testTransition Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends add_comments
     */
    public function issue_search()
    {
        $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        try {
            $issueService = new IssueService();

            $ret = $issueService->search($jql);
            $this->assertNotNull($ret);

            // Dumper::dump($ret);
        } catch (Exception $e) {
            $this->fail('testSearch Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends issue_search
     */
    public function testCustomField()
    {
        $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        try {
            $issueService = new IssueService();

            $ret = $issueService->search($jql);
            $this->assertNotNull($ret);

            //Dumper::dump($ret);
        } catch (Exception $e) {
            $this->fail('testSearch Failed : ' . $e->getMessage());
        }
    }
}
