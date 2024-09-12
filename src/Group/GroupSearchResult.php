<?php

namespace JiraRestApi\Group;

use JiraRestApi\ClassSerialize;

/**
 * Class GroupSearchResult.
 *
 *
 * @see https://docs.atlassian.com/jira/REST/server/#api/2/group
 */
class GroupSearchResult implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * uri which was hit.
     *
     * @var string
     */
    public $self;

    /**
     * @var int
     */
    public $maxResults;

    /**
     * @var int
     */
    public $startAt;

    /**
     * @var int
     */
    public $total;

    /** @var \JiraRestApi\User\User[] */
    public $values;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
