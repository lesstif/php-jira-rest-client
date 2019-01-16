<?php

namespace JiraRestApi\Board;

use JiraRestApi\ClassSerialize;

class Board implements \JsonSerializable
{
    use ClassSerialize;

    /** @var int */
    public $id;

    /** @var string */
    public $self;

    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /**
     * Location [\JiraRestApi\Board\Location].
     *
     * @var \JiraRestApi\Board\Location
     */
    public $location;

    /**
     * Get board id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get board url.
     */
    public function getSelf()
    {
        return $this->self;
    }

    /**
     * Get board name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get board type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get location.
     */
    public function getLocation()
    {
        return $this->location;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });
    }
}
