<?php

namespace JiraRestApi\Issue;

/**
 * Issue ChangeLog.
 *
 * Class ChangeLog
 */
class ChangeLog implements \JsonSerializable
{
    /** @var int */
    public $startAt;

    /** @var int */
    public $maxResults;

    /** @var int */
    public $total;

    /** @var \JiraRestApi\Issue\History[]|null */
    public $histories;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
