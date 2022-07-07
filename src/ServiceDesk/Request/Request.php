<?php

namespace JiraRestApi\ServiceDesk\Request;

use DateTime;
use DateTimeInterface;
use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class Request implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    /**
     * @var int
     */
    public $issueId;

    /**
     * @var string
     */
    public $issueKey;

    /**
     * @var string
     */
    public $requestTypeId;

    /**
     * @var string
     */
    public $serviceDeskId;

    /**
     * @var DateTime
     */
    public $createdDate;

    /**
     * @var Customer
     */
    public $reporter;

    /**
     * @var object
     */
    public $requestFieldValues;

    /**
     * @var RequestStatus
     */
    public $currentStatus;

    /**
     * @var Customer[]
     */
    public $requestParticipants = [];

    /**
     * @var object
     */
    public $_links;

    public function setRequestTypeId(string $requestTypeId): self
    {
        $this->requestTypeId = $requestTypeId;

        return $this;
    }

    public function setCreatedDate(object $createdDate): void
    {
        if (!$createdDate instanceof DateTimeInterface)
        {
            $createdDate = new DateTime($createdDate->iso8601);
        }

        $this->createdDate = $createdDate;
    }

    public function setReporter(object $reporter): self
    {
        if (!$reporter instanceof Customer)
        {
            $reporter = new Customer($reporter);
        }

        $this->reporter = $reporter;

        return $this;
    }

    public function setSummary(string $summary): self
    {
        $this->requestFieldValues['summary'] = $summary;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->requestFieldValues['description'] = $description;

        return $this;
    }

    public function addCustomField(string $key, $value): self
    {
        $this->requestFieldValues[$key] = $value;

        return $this;
    }

    private function setCurrentStatus(object $currentStatus): void
    {
        $this->currentStatus = new RequestStatus($currentStatus);
    }

    private function setLinks(object $links): void
    {
        $this->_links = $links;
    }

    /**
     * @param Customer[] $requestParticipants
     */
    public function setRequestParticipants(array $requestParticipants): self
    {
        $this->requestParticipants = $requestParticipants;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = get_object_vars($this);
        if ($this->reporter)
        {
            $data['raiseOnBehalfOf'] = $this->reporter->accountId ?? $this->reporter->emailAddress;
        }
        unset($data['reporter']);

        $data['requestParticipants'] = array_map(static function (Customer $customer): string {
            return $customer->accountId ?? $customer->emailAddress;
        }, $this->requestParticipants);

        return array_filter($data);
    }
}
