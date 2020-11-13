<?php declare(strict_types=1);

use JiraRestApi\Dumper;
use JiraRestApi\Exceptions\JiraException;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;

class IssueTest extends \PHPUnit\Framework\TestCase
{
    public function testIssue()
    {
        $this->markTestIncomplete();
        try {
            $issueService = new IssueService();

            $issue = $issueService->get('TEST-867');

            file_put_contents('jira-issue.json', json_encode($issue, JSON_PRETTY_PRINT));

            print_r($issue->fields->versions[0]);
        } catch (JiraException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /**
     * @test
     * @return string Issue key
     *
     * @throws JsonMapper_Exception
     */
    public function create_issue() :string
    {
        try {
            $issueField = new IssueField();

            $issueField->set_ProjectKey('TEST')
                ->set_Summary("something's wrong")
                ->set_AssigneeName('lesstif')
                ->set_PriorityName('Critical')
                ->set_IssueType('Bug')
                ->set_Description('Full description for issue')
                ->add_Version(['1.0.1', '1.0.3'])
                ->add_Components(['Component-1', 'Component-2'])
                ->set_DueDate('2019-06-19')
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
     * @test
     * @depends create_issue
     *
     * @param string $issueKey
     * @return string
     * @throws JsonMapper_Exception
     */
    public function get_previous_created_issue(string $issueKey) :string
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->get($issueKey);

            $this->assertEmpty($ret->fields->assignee);
            $this->assertEquals($ret->fields->issuetype->name, 'Bugs');

            print_r($ret);

            $issueKey = $ret->{'key'};

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     * @test
     *
     * @depends get_previous_created_issue
     * @param string $issueKey
     * @return string $issueKey
     */
    public function create_subtask_type_issue(string $issueKey) :string
    {
        try {
            $issueField = new IssueField();

            $issueField->set_ProjectKey('TEST')
                ->set_Summary("Subtask - something's wrong")
                ->set_AssigneeName('lesstif')
                ->set_PriorityName('Critical')
                ->set_Description('Subtask - Full description for issue')
                ->add_Version('1.0.1')
                ->add_Version('1.0.3')
                ->set_IssueType('Sub-task')
                ->set_ParentKeyOrId($issueKey);

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
     * @test
     *
     * @depends create_issue
     * @param string $issueKey
     * @recursive string $issueKey
     *
     */
    public function add_attachment(string $issueKey) :string
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
     * @test
     * @depends add_attachment
     * @param string $issueKey
     * @return string
     */
    public function update_issue(string $issueKey) :string
    {
        try {
            $issueField = new IssueField(true);

            $issueField->set_AssigneeName('lesstif')
                ->set_PriorityName('Major')
                ->set_IssueType('Task')
                ->add_Label('test-label-first')
                ->add_Label('test-label-second')
                ->add_Version('1.0.1')
                ->add_Version('1.0.2')
                ->set_Description('This is a shorthand for a set operation on the summary field');

            $issueService = new IssueService();

            $issueService->update($issueKey, $issueField);

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'update Failed : '.$e->getMessage());
        }
    }

    /**
     * @test
     *
     * @depends update_issue
     * @param string $issueKey
     * @return string
     */
    public function change_assignee(string $issueKey) :string
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
     * @test
     * @depends change_assignee
     * @depends search_issue_by_jql
     *
     * @param string $issueKey
     * @return string
     */
    public function delete_issue(string $issueKey):string
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
     * @test
     * @depends update_issue
     *
     * @param string $issueKey
     * @return string
     */
    public function add_comment(string $issueKey) :string
    {
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
     * @test
     * @depends add_comment
     *
     * @param string $issueKey
     * @return string
     */
    public function transit_issue(string $issueKey) :string
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
     * @test
     * @depends transit_issue
     */
    public function search_issue_by_jql()
    {
        $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        try {
            $issueService = new IssueService();

            $ret = $issueService->search($jql);
            //Dumper::dump($ret);

            $this->assertIsArray();
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
    }

    /**
     * @test
     * @depends search_issue_by_jql
     */
    public function custom_field()
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
