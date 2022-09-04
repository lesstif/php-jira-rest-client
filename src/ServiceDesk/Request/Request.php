<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Request;

use DateTime;
use DateTimeInterface;
use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonMapper;
use JsonSerializable;

class Request implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    public string $issueId;
    public string $issueKey;
    public string $requestTypeId;
    public ?string $serviceDeskId = null;
    public DateTimeInterface $createdDate;
    public ?Customer $reporter = null;
    public array $requestFieldValues;
    public RequestStatus $currentStatus;
    /**
     * @var Customer[]
     */
    public array $requestParticipants = [];
    public object $_links;

    public function setRequestTypeId(string $requestTypeId): self
    {
        $this->requestTypeId = $requestTypeId;

        return $this;
    }

    public function setServiceDeskId(string $serviceDeskId): self
    {
        $this->serviceDeskId = $serviceDeskId;

        return $this;
    }

    public function setCreatedDate(object $createdDate): void
    {
        if (!$createdDate instanceof DateTimeInterface) {
            $createdDate = new DateTime($createdDate->iso8601);
        }

        $this->createdDate = $createdDate;
    }

    public function setReporter(object $reporter): self
    {
        if (!$reporter instanceof Customer) {
            $reporter = $this->map($reporter, new Customer());
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

    public function setCurrentStatus(object $currentStatus): void
    {
        $this->currentStatus = $this->map($currentStatus, new RequestStatus());
    }

    public function setLinks(object $links): void
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
        if ($this->reporter) {
            $data['raiseOnBehalfOf'] = $this->reporter->accountId ?? $this->reporter->emailAddress;
        }
        unset($data['reporter']);

        $data['requestParticipants'] = array_map(static function (Customer $customer): string {
            return $customer->accountId ?? $customer->emailAddress;
        }, $this->requestParticipants);

        return array_filter($data);
    }

    private function map(object $data, object $target)
    {
        $mapper = new JsonMapper();

        return $mapper->map(
            $data,
            $target
        );
    }
}
