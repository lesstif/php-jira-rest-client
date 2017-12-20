<?php

use JiraRestApi\Issue\Comment;
use \Mockery as m;

class MapperTest extends PHPUnit_Framework_TestCase
{
    /** @var JsonMapper */
    public $mapper;

    public function setUp()
    {
        $this->mapper = new JsonMapper();
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
}
