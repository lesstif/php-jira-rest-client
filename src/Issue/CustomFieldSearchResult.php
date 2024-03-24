<?php

namespace JiraRestApi\Issue;

class CustomFieldSearchResult implements \JsonSerializable
{
    public int $maxResults;

    public int $startAt;

    public int $total;

    public bool $isLast;

    /**
     * @var array of CustomField
     */
    public array $values;

    public function jsonSerialize(): mixed
    {
        return array_filter(get_object_vars($this));
    }
}
