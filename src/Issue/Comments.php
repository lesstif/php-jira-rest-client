<?php

namespace JiraRestApi\Issue;

class Comments implements \JsonSerializable
{
    /** @var string */
    public $self;

    /** @var int */
    public $startAt;

    /** @var int */
    public $maxResults;

    /** @var int */
    public $total;

    /** @var \JiraRestApi\Issue\Comment[] */
    public $comments;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
