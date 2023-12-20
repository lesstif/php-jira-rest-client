<?php

namespace JiraRestApi\Auth;

use JiraRestApi\ClassSerialize;

class AuthSession implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * @var \JiraRestApi\Auth\SessionInfo
     */
    public $session;

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
