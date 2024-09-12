<?php

namespace JiraRestApi\Issue;

class Priority implements \JsonSerializable
{
    /** @var string */
    public $self;

    /** @var string */
    public $iconUrl;

    /** @var string */
    public $name;

    /** @var string */
    public $id;

    /** @var string */
    public $statusColor;

    /** @var string */
    public $description;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
