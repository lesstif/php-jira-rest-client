<?php

use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\SecurityScheme;
use \Mockery as m;

class MapperTest extends PHPUnit_Framework_TestCase
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

    public function testIssue()
    {
        $ret = file_get_contents('test-data/issue.json');

        $is = new \JiraRestApi\Issue\IssueService();
        $issue = $this->mapper->map(
                json_decode($ret), new Issue()
            );

        $this->assertInstanceOf(Issue::class, $issue);

        $this->assertInstanceOf(Reporter::class, $issue->fields->assignee);
        $this->assertEquals('lesstif@gmail.com', $issue->fields->assignee->emailAddress);

        //$this->assertInstanceOf(SecurityScheme::class, $issue->fields->security);
        //$this->assertEquals('KwangSeob Jeong', $issue->updateAuthor->name);
    }
}
