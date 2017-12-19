<?php

namespace JiraRestApi\Issue;

class TransitionTo
{
    /** @var string */
    public $self;

    /** @var string|null */
    public $description;

    /** @var string */
    public $iconUrl;

    /**
     * Closed, Resolved, etc..
     *
     * @var string
     */
    public $name;

    /** @var string */
    public $id;

    /** @var array */
    public $statusCategory;
}
