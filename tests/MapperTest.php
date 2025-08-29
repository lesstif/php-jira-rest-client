<?php declare(strict_types=1);

namespace JiraRestApi\Test;

use JsonMapper;
use PHPUnit\Framework\TestCase;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\SecurityScheme;
use \Mockery as m;

class MapperTest extends TestCase
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

    public function testComment()
    {
        $ret = file_get_contents('test-data/comment.json');

        $comment = $this->mapper->map(
            json_decode($ret), new Comment()
        );

        $this->assertInstanceOf(Comment::class, $comment);

        $this->assertEquals('johndoe@example.com', $comment->author->emailAddress);
        $this->assertEquals('KwangSeob Jeong', $comment->updateAuthor->name);
    }

    public function testIssueField()
    {
        $ret = file_get_contents('test-data/issueField.json');

        $issueField = $this->mapper->map(
            json_decode($ret), new IssueField()
        );

        $this->assertInstanceOf(IssueField::class, $issueField);

        $this->assertInstanceOf(Reporter::class, $issueField->assignee);
        $this->assertEquals('lesstif@gmail.com', $issueField->assignee->emailAddress);

        $this->assertInstanceOf(SecurityScheme::class, $issueField->security);
        $this->assertEquals(12345, $issueField->security->id);
    }

    public function testIssue()
    {
        $ret = file_get_contents('test-data/issue.json');

        $is = new \JiraRestApi\Issue\IssueService();
        $issue = $this->mapper->map(
                json_decode($ret), new Issue()
            );

        $this->assertInstanceOf(Issue::class, $issue);

        $this->assertTrue(is_array($issue->renderedFields));
        $this->assertArrayHasKey('description', $issue->renderedFields);
        $this->assertEquals(10000, $issue->renderedFields['attachment'][0]->id);

        $this->assertTrue(is_array($issue->names));
        $this->assertArrayHasKey('issuetype', $issue->names);
        $this->assertArrayHasKey('timespent', $issue->names);

        $this->assertTrue(is_array($issue->schema));
        $this->assertArrayHasKey('fixVersions', $issue->schema);
        $this->assertEquals('array', $issue->schema['fixVersions']->type);

        $this->assertTrue(is_array($issue->transitions));
        $this->assertLessThan(3, count($issue->transitions));
        $this->assertEquals('작업 시작', $issue->transitions[0]->name);

    }

    public function testReporterField()
    {
        $ret = file_get_contents('test-data/reporter-no-email-address.json');

        $reporter = $this->mapper->map(
            json_decode($ret), new Reporter()
        );

        $this->assertInstanceOf(Reporter::class, $reporter);

        $this->assertEquals('lesstif@gmail.com', $reporter->emailAddress);
    }

}
