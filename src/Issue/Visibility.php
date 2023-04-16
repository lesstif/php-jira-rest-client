<?php

namespace JiraRestApi\Issue;

class Visibility implements \JsonSerializable
{
    private string $type;
    private string $value;

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
