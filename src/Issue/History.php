<?php

namespace JiraRestApi\Issue;

/**
 * ChangeLog History
 *
 * Class History
 *
 * @package JiraRestApi\Issue
 */
class History implements \JsonSerializable
{
    /** @var integer */
    public $id;

    /** @var Reporter */
    public $author;

    /** @var string */
    public $created;

    /** @var array|null */
    public $items;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
