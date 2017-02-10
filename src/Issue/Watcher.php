<?php

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;
use JiraRestApi\JiraException;

/**
 * Class Watcher.
 */
class Watcher implements \JsonSerializable
{
    use ClassSerialize;

    /** @var  string  */
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Function to serialize obj vars.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->name;
    }

}
