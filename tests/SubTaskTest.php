<?php declare(strict_types=1);

use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Exceptions\JiraException;
use JiraRestApi\Exceptions\HTTPException;

class SubTaskTest extends \PHPUnit\Framework\TestCase
{
    public $issueKey = 'TEST-143';

    public function testCreateSubTask()
    {
        try {
            $issueField = new IssueField();

            $issueField->set_ProjectKey('TEST')
                ->set_Summary("Subtask - something's wrong")
                ->set_AssigneeName('lesstif')
                ->set_PriorityName('Critical')
                ->set_IssueType('Sub-task')
                ->set_Description('Subtask - Full description for issue')
                ->add_Version('1.0.1')
                ->add_Version('1.0.3')
                ->set_ParentKeyOrId($this->issueKey);

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
}
