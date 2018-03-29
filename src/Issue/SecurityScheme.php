<?php

namespace JiraRestApi\Issue;

class SecurityScheme implements \JsonSerializable
{
    /** @var string */
    public $self;

    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var int */
    public $defaultSecurityLevelId;

    /** @var array security level */
    public $levels;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
