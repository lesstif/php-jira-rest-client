<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Customer;

use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class CustomerLinks implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    public string $jiraRest;
    public object $avatarUrls;
}
