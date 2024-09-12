<?php

namespace JiraRestApi;

trait JsonSerializableTrait
{
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
