<?php

namespace JiraRestApi\ServiceDesk\Organisation;

use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class Organisation implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var object
     */
    public $_links;

    private function setLinks(array $links): void
    {
        $this->_links = $links;
    }
}