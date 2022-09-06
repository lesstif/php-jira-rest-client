<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Customer;

use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class Customer implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    public string $key;
    public string $name;
    public string $accountId;
    public string $emailAddress;
    public string $displayName;
    public bool $active;
    public string $timeZone;
    public ?CustomerLinks $_links;
    public string $self;

    public function setLinks($links): void
    {
        if ($links === null) {
            return;
        }

        if (!$links instanceof CustomerLinks) {
            $data = $links;

            $links = new CustomerLinks($data);
        }

        $this->_links = $links;
    }
}
