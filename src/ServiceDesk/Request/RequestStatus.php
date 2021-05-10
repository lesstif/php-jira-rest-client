<?php

namespace JiraRestApi\ServiceDesk\Request;

use DateTime;
use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class RequestStatus implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    /**
     * @var string
     */
    public $status;

    /**
     * @var DateTime
     */
    public $statusDate;

    private function setStatusDate(string $statusDate): void
    {
        $this->statusDate = new DateTime($statusDate['iso8601']);
    }
}