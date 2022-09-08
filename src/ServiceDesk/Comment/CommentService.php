<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Comment;

use InvalidArgumentException;
use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonException;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;

class CommentService
{
    private ServiceDeskClient $client;
    private string $uri = '/request';
    private LoggerInterface $logger;
    private JsonMapper $jsonMapper;

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
        $this->logger = $client->getLogger();
        $this->jsonMapper = $client->getMapper();
    }

    /**
     * @throws JiraException|JsonMapper_Exception|JsonException
     */
    public function addComment(string $issueId, Comment $comment): Comment
    {
        $this->logger->info("addComment=\n");

        if (empty($comment->body)) {
            throw new JiraException('comment param must have body text.');
        }

        $data = json_encode($comment, JSON_THROW_ON_ERROR);

        $result = $this->client->exec(
            $this->client->createUrl('%s/%d/comment', [$this->uri, $issueId]),
            $data
        );

        $this->logger->debug('add comment result='.var_export($result, true));

        return $this->jsonMapper->map(
            json_decode($result, false, 512, JSON_THROW_ON_ERROR),
            new Comment()
        );
    }

    /**
     * @throws JiraException|JsonMapper_Exception|JsonException
     */
    public function getComment(string $issueId, int $commentId): Comment
    {
        $this->logger->info("getComment=\n");

        $result = $this->client->exec(
            $this->client->createUrl('%s/%d/comment/%d', [$this->uri, $issueId, $commentId])
        );

        $this->logger->debug('get comment result='.var_export($result, true));

        return $this->jsonMapper->map(
            json_decode($result, false, 512, JSON_THROW_ON_ERROR),
            new Comment()
        );
    }

    /**
     * @throws JiraException|JsonMapper_Exception|InvalidArgumentException|JsonException
     *
     * @return Comment[]
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request/{issueIdOrKey}/comment-getRequestComments
     */
    public function getCommentsForRequest(string $issueId, bool $showPublicComments = true, bool $showInternalComments = true, int $startIndex = 0, int $amountOfItems = 50): array
    {
        $this->logger->info("getComments for request=\n");

        $searchParameters = $this->getRequestSearchParameters(
            $showPublicComments,
            $showInternalComments,
            $startIndex,
            $amountOfItems
        );

        $result = $this->client->exec(
            $this->client->createUrl('%s/%d/comment', [$this->uri, $issueId], $searchParameters)
        );

        $this->logger->debug('get comments result='.var_export($result, true));

        $commentData = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        $comments = [];
        foreach ($commentData as $comment) {
            $comments[] = $this->jsonMapper->map(
                $comment,
                new Comment()
            );
        }

        return $comments;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getRequestSearchParameters(bool $showPublicComments, bool $showInternalComments, int $startIndex, int $amountOfItems): array
    {
        if ($startIndex < 0) {
            throw new InvalidArgumentException('Start index can not be lower then 0.');
        }
        if ($amountOfItems < 1) {
            throw new InvalidArgumentException('Amount of items can not be lower then 1.');
        }

        return [
            'public'   => $showPublicComments,
            'internal' => $showInternalComments,
            'start'    => $startIndex,
            'limit'    => $amountOfItems,
        ];
    }
}
