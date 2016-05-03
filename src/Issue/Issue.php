<?php

namespace JiraRestApi\Issue;

class Issue implements \JsonSerializable
{
    /**
     * return only if Project query by key(not id).
     *
     * @var string
     */
    public $expand;

    /**
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @var IssueField
     */
    public $fields;

    /** @var string[] */
    public $names;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
