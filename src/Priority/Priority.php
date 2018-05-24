<?php

namespace JiraRestApi\Priority;

use JiraRestApi\ClassSerialize;

/**
 * Description of Priority.
 */
class Priority implements \JsonSerializable
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
    public $statusColor;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $iconUrl;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $id;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    /**
     * Priority constructor.
     *
     * @param array $array priority info array.
     */
    public function __construct($array = [])
    {
        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
