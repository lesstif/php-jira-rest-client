<?php

namespace JiraRestApi\ServiceDesk\Customer;

use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class Customer implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $accountId;

    /**
     * @var string
     */
    public $emailAddress;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var string
     */
    public $timeZone;

    /**
     * @var CustomerLinks|null
     */
    public $_links;

    /**
     * @var string
     */
    public $self;

    private function setLinks($links): void
    {
        if (!$links instanceof CustomerLinks)
        {
            $links = new CustomerLinks($links);
        }

        $this->_links = $links;
    }
}
