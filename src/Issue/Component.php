<?php

namespace JiraRestApi\Issue;

class Component implements \JsonSerializable
{
    public $id;
    public $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function jsonSerialize(): mixed
    {
        return array_filter(get_object_vars($this));
    }
}
