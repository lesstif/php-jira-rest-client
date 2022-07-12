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

    public function setAuthor(array $author): void
    {
        $this->author = new Reporter();
        foreach ($author as $key => $value) {
            if (property_exists($this->author, $key)) {
                $this->author->$key = $value;
            }
        }
    }

    public function setCreated(array $created): void
    {
        $this->created = new DateTime($created['iso8601']);
    }

    public function setLinks(array $links): void
    {
        $this->_links = $links;
    }
}
