<?php

namespace JiraRestApi\Auth;

use JiraRestApi\ClassSerialize;

class SessionInfo implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
