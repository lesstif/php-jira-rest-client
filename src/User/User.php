<?php

namespace JiraRestApi\User;
use JiraRestApi\ClassSerialize;

/**
 * Description of User.
 *
 * @author Anik
 */
class User implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * uri which was hit.
     *
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $emailAddress;

    /**
     * @var object
     */
    public $avatarUrls;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var string
     */
    public $timeZone;

    /**
     * @var array "#/definitions/simple-list-wrapper"
     */
    public $groups;

    /**
     * @var array "#/definitions/simple-list-wrapper"
     */
    public $applicationRoles;

    /**
     * @var string
     */
    public $expand;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
