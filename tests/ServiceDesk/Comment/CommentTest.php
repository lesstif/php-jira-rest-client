<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Comment;

use DateTimeInterface;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\ServiceDesk\Comment\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testSetId(): void
    {
        $id = '152';

        $uut = new Comment();
        $uut->setId($id);

        self::assertSame((int)$id, $uut->id);
    }

    public function testSetAuthor(): void
    {
        $author = [
            'name' => 'test reporter',
            'active' => '1',
            'emailAddress' => 'reporter@example.com',
            'not_real' => 'true',
        ];

        $uut = new Comment();
        $uut->setAuthor($author);

        self::assertInstanceOf(Reporter::class, $uut->author);
        self::assertSame($author['name'], $uut->author->name);
        self::assertSame($author['active'], $uut->author->active);
        self::assertSame($author['emailAddress'], $uut->author->emailAddress);
    }

    public function testSetCreated(): void
    {
        $created = [
            'timestamp' => 21543521312311,
            'iso8601' => '2022/05/21',
        ];

        $uut = new Comment();
        $uut->setCreated($created);

        self::assertInstanceOf(DateTimeInterface::class, $uut->created);
        self::assertSame('2022/05/21', $uut->created->format('Y/m/d'));
    }

    public function testSetLinks(): void
    {
        $links = [
            ['url' => 'https://example.com/images/image_1.jpeg'],
            ['url' => 'https://example.com/images/image_3.jpeg'],
            ['url' => 'https://example.com/images/image_6.jpeg'],
        ];

        $uut = new Comment();
        $uut->setLinks($links);

        self::assertSame($links, $uut->_links);
    }
}
