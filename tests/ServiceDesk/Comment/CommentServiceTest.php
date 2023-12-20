<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Comment;

use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\Comment\Comment;
use JiraRestApi\ServiceDesk\Comment\CommentService;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommentServiceTest extends TestCase
{
    private string $uri = '/request';

    public function testAddCommentWithNoBody(): void
    {
        $this->expectException(JiraException::class);

        $uut = new CommentService(
            $this->createClient()
        );

        $uut->addComment('10', new Comment());
    }

    public function testAddComment(): void
    {
        $comment = new Comment();
        $comment->body = 'test comment';
        $comment->id = 15;

        $url = 'https://example.com/add-comment';

        $item = $this->createItem($comment->body, $comment->id);
        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%d/comment', [$this->uri, 10])
            ->willReturn($url);
        $client->method('exec')
            ->with($url, '{"id":15,"body":"test comment","public":true}')
            ->willReturn($item);

        $uut = new CommentService($client);
        $result = $uut->addComment('10', $comment);

        self::assertSame($comment->body, $result->body);
        self::assertSame($comment->id, $result->id);
    }

    public function testGetComment(): void
    {
        $url = 'https://example.com/get-comment';

        $item = $this->createItem('test comment', 123);
        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%d/comment/%d', [$this->uri, 42, 123])
            ->willReturn($url);
        $client->method('exec')
            ->with($url)
            ->willReturn($item);

        $uut = new CommentService($client);
        $result = $uut->getComment('42', 123);

        self::assertSame('test comment', $result->body);
        self::assertSame(123, $result->id);
    }

    public function testGetCommentsForRequest(): void
    {
        $items = json_encode([
            ['body' => 'item 1', 'id' => 15],
            ['body' => 'item 3', 'id' => 45],
            ['body' => 'item 10', 'id' => 65],
        ]);
        $url = 'https://example.com/get-comments';
        $searchParameters = [
            'public' => true,
            'internal' => true,
            'start' => 0,
            'limit' => 50,
        ];

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%d/comment', [$this->uri, 43], $searchParameters)
            ->willReturn($url);
        $client->method('exec')
            ->with($url)
            ->willReturn($items);

        $uut = new CommentService($client);
        $results = $uut->getCommentsForRequest('43');

        self::assertCount(3, $results);
        self::assertSame('item 1', $results[0]->body);
        self::assertSame(15, $results[0]->id);
        self::assertSame('item 3', $results[1]->body);
        self::assertSame(45, $results[1]->id);
        self::assertSame('item 10', $results[2]->body);
        self::assertSame(65, $results[2]->id);
    }

    /**
     * @return ServiceDeskClient|MockObject
     */
    private function createClient(): MockObject|ServiceDeskClient
    {
        $mapper = new JsonMapper();

        $client = $this->createMock(ServiceDeskClient::class);
        $client->method('getMapper')->willReturn($mapper);

        return $client;
    }

    private function createItem(string $body, int $id): string
    {
        return json_encode([
            'body' => $body,
            'id' => $id,
        ]);
    }
}
