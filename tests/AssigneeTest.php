<?php

namespace JiraRestApi\Test;

use JsonMapper;
use PHPUnit\Framework\TestCase;
use JiraRestApi\Dumper;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\Version;
use \Mockery as m;

class AssigneeTest extends TestCase
{
    /** @var JsonMapper */
    public $mapper;

    public function setUp(): void
    {
        $this->mapper = new JsonMapper();
        $this->mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];
        $this->mapper->classMap['\\'.\DateTimeInterface::class] = \DateTime::class;
    }

    public function tearDown(): void
    {
        $this->mapper = null;
        m::close();
    }

    public function testAssigneeFieldNull()
    {
        $issueField = new IssueField();

        $issueField->setProjectKey('TEST')
            ->setIssueTypeAsString('Bug')
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayNotHasKey('assignee', $js);
    }

    public function testUnassigned()
    {
        $issueField = new IssueField();

        $issueField->setProjectKey('TEST')
            ->setIssueTypeAsString('Bug')
            ->setAssigneeToUnassigned()
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayHasKey('assignee', $js);

        $assignee = $js['assignee'];

        $this->assertEquals(true, property_exists($assignee, 'name'), "Reporter class has not 'name' property");
        $this->assertEquals(null, $assignee->name, "name field not equal to 'null'");
    }

    public function testAssigneeFieldDefault()
    {
        $issueField = new IssueField();

        $issueField->setProjectKey('TEST')
            ->setIssueTypeAsString('Bug')
            ->setAssigneeToDefault()
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayHasKey('assignee', $js);

        $assignee = $js['assignee'];

        $this->assertEquals(true, property_exists($assignee, 'name'), "Reporter class has not 'name' property");
        $this->assertEquals("-1", $assignee->name, "name field not equal to '-1'");
    }

    public function testAssigneeFieldHasAssignee()
    {
        $issueField = new IssueField();

        $issueField->setProjectKey('TEST')
            ->setIssueTypeAsString('Bug')
            ->setAssigneeNameAsString('lesstif')
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayHasKey('assignee', $js);

        $assignee = $js['assignee'];

        $this->assertEquals(true, property_exists($assignee, 'name'), "Reporter class has not 'name' property");
        $this->assertEquals("lesstif", $assignee->name, "name field not equal to ");
    }
}
