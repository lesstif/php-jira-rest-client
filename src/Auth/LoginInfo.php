<?php

namespace JiraRestApi\Auth;

use JiraRestApi\ClassSerialize;

class LoginInfo implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * @var int
     */
    public $failedLoginCount;

    /**
     * @var int
     */
    public $loginCount;

    /**
     * timestamp string "2017-12-07T09:23:17.771+0000".
     *
     * @var string
     */
    public $lastFailedLoginTime;

    /**
     * timestamp string "2017-12-07T09:23:17.771+0000".
     *
     * @var string
     */
    public $previousLoginTime;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
