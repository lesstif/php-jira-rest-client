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

    /** @var string */
    public $version;

    public function __construct()
    {
        $this->content = [];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
