<?php

namespace JiraRestApi\ServiceDesk\Comment;

use InvalidArgumentException;
use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper_Exception;

class CommentService
{
    /**
     * @var ServiceDeskClient
     */
    private $client;

    /**
     * @var string
     */
    private $uri = '/servicedeskapi/request';

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function addComment(int $issueId, Comment $comment): Comment
    {
        $this->client->log("addComment=\n");

        if (empty($comment->body)) {
            throw new JiraException('comment param must have body text.');
        }

        $data = json_encode($comment);

        $result = $this->client->exec(
            $this->client->createUrl('%s/%d/comment', [$this->uri, $issueId,]),
            $data
        );

        $this->client->getLogger()->debug('add comment result=' . var_export($result, true));

        return $this->client->map($result, new Comment());
    }

    /**
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function getComment(int $issueId, int $commentId): Comment
    {
        $this->client->log("getComment=\n");

        $result = $this->client->exec(
            $this->client->createUrl('%s/%d/comment/%d', [$this->uri, $issueId, $commentId,])
        );

        $this->client->getLogger()->debug('get comment result=' . var_export($result, true));

        return $this->client->map($result, new Comment());
    }

    /**
     * @return Comment[]
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     * @throws InvalidArgumentException
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request/{issueIdOrKey}/comment-getRequestComments
     */
    public function getCommentsForRequest(
        int $issueId,
        bool $showPublicComments = true,
        bool $showInternalComments = true,
        int $startIndex = 0,
        int $amountOfItems = 50
    ): array {
        $this->client->log("getComments for request=\n");

        $searchParameters = $this->getRequestSearchParameters(
            $showPublicComments,
            $showInternalComments,
            $startIndex,
            $amountOfItems
        );

        $result = $this->client->exec(
            $this->client->createUrl('%s/%d/comment', [$this->uri, $issueId,], $searchParameters)
        );

        $this->client->getLogger()->debug('get comments result=' . var_export($result, true));

        $commentData = json_decode($result, false);

        $comments = [];
        foreach ($commentData as $comment) {
            $comments[] = $this->client->mapWithoutDecode($comment, new Comment());
        }

        return $comments;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getRequestSearchParameters(
        bool $showPublicComments,
        bool $showInternalComments,
        int $startIndex,
        int $amountOfItems
    ): array {
        if ($startIndex < 0) {
            throw new InvalidArgumentException('Start index can not be lower then 0.');
        }
        if ($amountOfItems < 1) {
            throw new InvalidArgumentException('Amount of items can not be lower then 1.');
        }

        return [
            'public' => $showPublicComments,
            'internal' => $showInternalComments,
            'start' => $startIndex,
            'limit' => $amountOfItems,
        ];
    }
}