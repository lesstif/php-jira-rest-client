<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\Version;
use \Mockery as m;

class AssigneeTest extends PHPUnit_Framework_TestCase
{
    /** @var JsonMapper */
    public $mapper;

    public function setUp()
    {
        $this->mapper = new JsonMapper();
        $this->mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];
    }

    public function tearDown()
    {
        $this->mapper = null;
        m::close();
    }

    public function testAssigneeFieldNull()
    {
        $issueField = new IssueField();

        $issueField->setProjectKey('TEST')
            ->setIssueType('Bug')
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayHasKey('assignee', $js);
        Dumper::dd($js);
    }

    public function testUnassigned()
    {
        $issueField = new IssueField();

        $issueField->setProjectKey('TEST')
            ->setIssueType('Bug')
            ->setAssigneeToUnassigned()
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayNotHasKey('assignee', $js);
    }

    public function testAssigneeFieldDefault()
    {
        $issueField = new IssueField();

        $issueField->setProjectKey('TEST')
            ->setIssueType('Bug')
            ->setAssigneeToDefault()
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayHasKey('assignee', $js);
    }

    public function testAssigneeFieldHasAssignee()
    {
        $issueField = new IssueField();

        $issueField->setProjectKey('TEST')
            ->setIssueType('Bug')
            ->setAssigneeName('lesstif')
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayHasKey('assignee', $js);
    }
}
