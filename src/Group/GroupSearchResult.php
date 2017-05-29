<?php

namespace JiraRestApi\Group;
use JiraRestApi\ClassSerialize;

/**
 * Class GroupSearchResult
 *
 * @package JiraRestApi\Group
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
     * @var integer
     */
    public $maxResults;

    /**
     * @var integer
     */
    public $startAt;

    /**
     * @var integer
     */
    public $total;

    /** @var  \JiraRestApi\User\User[] */
    public $values;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
