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

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
