<?php

namespace JiraRestApi\Issue;

class Version implements \JsonSerializable
{
    /** @var string */
    public $self;

    /** @var string */
    public $id;

    /** @var string Version name: ex: 4.2.3 */
    public $name;

    /** @var string|null version description: ex; improvement performance */
    public $description;

    /** @var bool */
    public $archived;

    /** @var bool */
    public $released;

    /** @var \DateTime|null */
    public $releaseDate;

    /** @var bool */
    public $overdue;

    /** @var string|null */
    public $userReleaseDate;

    /** @var int */
    public $projectId;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
