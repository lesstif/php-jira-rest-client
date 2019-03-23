<?php

namespace JiraRestApi\Issue;

class ContentField implements \JsonSerializable
{
    /** @var string */
    public $type;

    /** @var array */
    public $content;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}