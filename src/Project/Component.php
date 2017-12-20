<?php

namespace JiraRestApi\Project;

class Component
{
    /**
     * Component URI.
     *
     * @var string
     */
    public $self;

    /**
     * Component id.
     *
     * @var string
     */
    public $id;

    /**
     * Component name.
     *
     * @var string
     */
    public $name;

    /**
     * Component description.
     *
     * @var string|null
     */
    public $description;
}
