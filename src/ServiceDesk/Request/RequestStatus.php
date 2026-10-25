<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Request;

use DateTime;
use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class RequestStatus implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    public string $status;
    public DateTime $statusDate;

    // JsonMapper passes the JSON object as stdClass, so accept both array and object.
    public function setStatusDate($statusDate): void
    {
        $this->statusDate = new DateTime(((array) $statusDate)['iso8601']);
    }
}
