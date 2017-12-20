<?php

namespace JiraRestApi\Issue;

class Visibility implements \JsonSerializable
{
    private $type;
    private $value;

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
