<?php

namespace JiraRestApi\IssueType;

class IssueType implements \JsonSerializable
{

    /** @var string */
    public $self;

    /** @var int */
    public $id;

    /** @var string */
    public $description;

    /** @var string */
    public $iconUrl;

    /** @var string */
    public $name;

    /** @var boolean */
    public $subtask;

    /** @var integer */
    public $avatarId;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
