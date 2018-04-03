<?php

namespace JiraRestApi\Issue;

/**
 * Issue ChangeLog
 *
 * Class ChangeLog
 *
 * @package JiraRestApi\Issue
 */
class ChangeLog implements \JsonSerializable
{
    /** @var integer */
    public $startAt;

    /** @var integer */
    public $maxResults;

    /** @var integer */
    public $total;

    /** @var History[]|null */
    public $histories;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
