<?php

namespace JiraRestApi;

trait JsonSerializableTrait
{
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
