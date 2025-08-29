<?php

namespace JiraRestApi\Status;

class Status implements \JsonSerializable
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string|null */
    public ?string $description = null;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
