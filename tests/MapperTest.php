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

        // Mirror the JsonMapper configuration applied in JiraClient::__construct().
        if (property_exists($this->mapper, 'bStrictNullTypesInArrays')) {
            $this->mapper->bStrictNullTypesInArrays = false;
        }
        $this->mapper->bStrictNullTypes = false;
        $this->mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];

        // Properties typed as \DateTimeInterface should be created as \DateTime.
        // Class mapping alone is not enough: strict object type checking must be
        // disabled so JsonMapper passes the date string to the DateTime constructor.
        $this->mapper->classMap['\\'.\DateTimeInterface::class] = \DateTime::class;
        $this->mapper->bStrictObjectTypeChecking = false;
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

        $this->assertInstanceOf(\DateTime::class, $comment->created);
        $this->assertInstanceOf(\DateTime::class, $comment->updated);
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

        $this->assertInstanceOf(\DateTime::class, $issueField->created);
        $this->assertInstanceOf(\DateTime::class, $issueField->updated);
    }

    public function testIssue()
    {
        $ret = file_get_contents('test-data/issue.json');

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

    /**
     * @see https://github.com/lesstif/php-jira-rest-client/issues/522
     */
    public function testReporterField()
    {
        $ret = file_get_contents('test-data/reporter-no-email-address.json');

        $reporter = $this->mapper->map(
            json_decode($ret)->reporter, new Reporter()
        );

        $this->assertInstanceOf(Reporter::class, $reporter);

        $this->assertEquals('lesstif', $reporter->name);
        $this->assertEquals('lesstif', $reporter->key);

        // emailAddress is absent from the JSON. It must be readable and null,
        // not raise "must not be accessed before initialization".
        $this->assertNull($reporter->emailAddress);
    }

}
