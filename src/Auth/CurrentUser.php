<?php

namespace JiraRestApi\Auth;

use JiraRestApi\ClassSerialize;

class CurrentUser implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \JiraRestApi\Auth\LoginInfo
     */
    public $loginInfo;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
