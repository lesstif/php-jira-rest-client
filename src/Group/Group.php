<?php

namespace JiraRestApi\Group;

use JiraRestApi\ClassSerialize;

/**
 * Class Group.
 *
 *
 * @see https://docs.atlassian.com/jira/REST/server/#api/2/group
 */
class Group implements \JsonSerializable
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
    public $name;

    /**
     * @var GroupUser
     */
    public $users;

    /**
     * @var object
     */
    public $expand;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
