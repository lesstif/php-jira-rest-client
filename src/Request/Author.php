<?php

namespace JiraRestApi\Request;

class Author implements \JsonSerializable
{
    /** @var string */
    public $name;

    /** @var string */
    public $key;

    /** @var string */
    public $emailAddress;

    /** @var string */
    public $displayName;

    /** @var bool */
    public $active;

    /** @var string */
    public $timeZone;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
