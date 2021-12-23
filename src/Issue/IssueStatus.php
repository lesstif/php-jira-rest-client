<?php

namespace JiraRestApi\Issue;

class IssueStatus implements \JsonSerializable
{
    /* @var string */
    public $self;

    /* @var string */
    public $id;

    /* @var string|null */
    public $description;

    /* @var string */
    public $iconUrl;

    /* @var string */
    public $name;

    /* @var \JiraRestApi\Issue\Statuscategory */
    public $statuscategory;

    public function jsonSerialize(): mixed
    {
        return array_filter(get_object_vars($this));
    }
}
