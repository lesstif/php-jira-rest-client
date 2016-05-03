<?php

namespace JiraRestApi\Issue;

class Version implements \JsonSerializable
{
    /**
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $archived;

    /**
     * @var bool
     */
    public $released;

    /**
     * @var \DateTime
     */
    public $releaseDate;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
