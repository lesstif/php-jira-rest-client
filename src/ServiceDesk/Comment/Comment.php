<?php

declare(strict_types=1);

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

    public int $id;
    public string $body;
    public bool $public = true;
    public Reporter $author;
    public DateTimeInterface $created;
    public array $_links;

    public function setId(string $id): void
    {
        $this->id = (int) $id;
    }

    // JsonMapper passes the JSON object as stdClass, so accept both array and object.
    public function setAuthor($author): void
    {
        $this->author = new Reporter();
        foreach ((array) $author as $key => $value) {
            if (property_exists($this->author, $key)) {
                $this->author->$key = $value;
            }
        }
    }

    public function setCreated($created): void
    {
        $this->created = new DateTime(((array) $created)['iso8601']);
    }

    public function setLinks($links): void
    {
        $this->_links = (array) $links;
    }
}
