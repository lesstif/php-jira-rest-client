<?php

declare(strict_types=1);

namespace JiraRestApi\Issue;

class Issue implements \JsonSerializable
{
    /**
     * return only if Project query by key(not id).
     *
     * @var string|null
     */
    public $expand;

    /** @var string */
    public $self;

    /** @var string */
    public $id;

    /** @var string */
    public $key;

    /** @var \JiraRestApi\Issue\IssueField */
    public $fields;

    /** @var array */
    public $renderedFields;

    /** @var array */
    public $names;

    /** @var array */
    public $schema;

    /** @var array */
    public $transitions;

    /** @var array|null */
    public $operations;

    /** @var array|null */
    public $editmeta;

    /** @var \JiraRestApi\Issue\ChangeLog */
    public $changelog;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
