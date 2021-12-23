<?php

namespace JiraRestApi;

trait JsonSerializableTrait
{
    public function jsonSerialize(): mixed
    {
        return array_filter(get_object_vars($this));
    }
}
