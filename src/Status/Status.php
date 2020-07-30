<?php

namespace JiraRestApi\Status;


class Status implements \JsonSerializable
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
