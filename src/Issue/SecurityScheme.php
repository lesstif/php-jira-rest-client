<?php
namespace JiraRestApi\Issue;

class SecurityScheme implements \JsonSerializable
{
    /** @var string */
    public $self;

    /** @var integer */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var integer */
    public $defaultSecurityLevelId;

    /** @var array security level */
    public $levels;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}