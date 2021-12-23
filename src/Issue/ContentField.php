<?php

namespace JiraRestApi\Issue;

class ContentField implements \JsonSerializable
{
    /** @var string */
    public $type;

    /** @var array */
    public $content;

    /** @var array */
    public $attrs;

    public function __construct()
    {
        $this->content = [];
    }

    public function jsonSerialize(): mixed
    {
        return array_filter(get_object_vars($this));
    }
}
