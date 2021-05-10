<?php

namespace JiraRestApi\ServiceDesk\Comment;

use DateTime;
use DateTimeInterface;
use JiraRestApi\ClassSerialize;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class Comment implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $body;

    /**
     * @var bool
     */
    public $public = true;

    /**
     * @var Reporter
     */
    public $author;

    /**
     * @var DateTimeInterface
     */
    public $created;

    /**
     * @var array
     */
    public $_links;

    private function setId(string $id): void
    {
        $this->id = (int)$id;
    }

    private function setAuthor(array $author): void
    {
        $this->author = new Reporter($author);
    }

    private function setCreated(array $created): void
    {
        $this->created = new DateTime($created['iso8601']);
    }

    private function setLinks(array $links): void
    {
        $this->_links = $links;
    }
}