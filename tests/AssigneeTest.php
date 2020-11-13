<?php declare(strict_types=1);

use JiraRestApi\Dumper;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\Version;
use \Mockery as m;

class AssigneeTest extends \PHPUnit\Framework\TestCase
{
    /** @var JsonMapper */
    public $mapper;

    public function setUp() : void
    {
        $this->mapper = new JsonMapper();
        $this->mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];
        $this->mapper->classMap['\\'.\DateTimeInterface::class] = \DateTime::class;
    }

    public function tearDown() : void
    {
        $this->mapper = null;
        m::close();
    }

    public function testAssigneeFieldNull()
    {
        $issueField = new IssueField();

        $issueField->set_ProjectKey('TEST')
            ->set_IssueType('Bug')
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayNotHasKey('assignee', $js);
    }

    public function testUnassigned()
    {
        $issueField = new IssueField();

        $issueField->set_ProjectKey('TEST')
            ->set_IssueType('Bug')
            ->set_AssigneeToUnassigned()
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

        $issueField->set_ProjectKey('TEST')
            ->set_IssueType('Bug')
            ->set_AssigneeToDefault()
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

        $issueField->set_ProjectKey('TEST')
            ->set_IssueType('Bug')
            ->set_AssigneeName('lesstif')
        ;

        $js = $issueField->jsonSerialize();

        $this->assertArrayHasKey('assignee', $js);

        $assignee = $js['assignee'];

        $this->assertEquals(true, property_exists($assignee, 'name'), "Reporter class has not 'name' property");
        $this->assertEquals("lesstif", $assignee->name, "name field not equal to ");
    }
}
