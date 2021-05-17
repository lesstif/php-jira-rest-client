<?php

namespace JiraRestApi\ServiceDesk\Organisation;

use InvalidArgumentException;
use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper_Exception;

class OrganisationService
{
    /**
     * @var ServiceDeskClient
     */
    private $client;

    /**
     * @var string
     */
    private $uri = '/servicedeskapi/organization';

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/organization-createOrganization
     */
    public function create(array $data): Organisation
    {
        $this->client->log("Create ServiceDesk Organisation=\n" . $data);

        $result = $this->client->exec($this->uri, $data, 'POST');

        return $this->createOrganisation($result);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/organization-createOrganization
     */
    public function createFromOrganisation(Organisation $organisation): Organisation
    {
        $data = json_encode($organisation);

        return $this->create($data);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function get(string $organisationId): Organisation
    {
        $result = $this->client->exec(
            $this->client->createUrl('%s/%s', [$this->uri, $organisationId,])
        );

        return $this->createOrganisation($result);
    }

    /**
     * Returns the organisations paginated
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/organization
     */
    public function getOrganisations(int $startIndex, int $amountOfItems): array
    {
        $result = $this->client->exec(
            $this->createGetOrganisationsUrl($startIndex, $amountOfItems)
        );

        $this->client->log("Result=\n" . $result);

        $organisationData = json_decode($result, false);
        $organisations = [];

        foreach ($organisationData->values as $organisation) {
            $organisations[] = $this->client->mapWithoutDecode($organisation, new Organisation());
        }

        return $organisations;
    }

    /**
     * Returns the organisation customers paginated
     *
     * @return Customer[]
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getCustomersForOrganisation(int $startIndex, int $amountOfItems, Organisation $organisation): array
    {
        $result = $this->client->exec(
            $this->createGetCustomersUrl($organisation->id, $startIndex, $amountOfItems)
        );

        $this->client->log("Result=\n" . $result);

        $customerData = json_decode($result, false);
        $customers = [];

        foreach ($customerData as $customer) {
            $customers[] = $this->client->mapWithoutDecode(
                $customer,
                new Customer()
            );
        }

        return $customers;
    }

    /**
     * @param Customer[] $customers
     *
     * @throws JiraException
     */
    public function addCustomersToOrganisation(array $customers, Organisation $organisation): void
    {
        $customerNames = array_map(
            static function (Customer $customer): string {
                return $customer->name;
            },
            $customers
        );

        $this->client->exec(
            $this->client->createUrl('%s/%s', [$this->uri, $organisation->id,]),
            ['usernames' => $customerNames],
            'POST'
        );
    }

    /**
     * @throws JiraException
     */
    public function deleteOrganisation(Organisation $organisation): void
    {
        $this->client->exec(
            $this->client->createUrl('%s/%s', [$this->uri, $organisation->id,]),
            null,
            'DELETE'
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createGetOrganisationsUrl(int $startIndex, int $amountOfItems): string
    {
        if ($startIndex < 0) {
            throw new InvalidArgumentException('Start index can not be lower then 0.');
        }
        if ($amountOfItems < 1) {
            throw new InvalidArgumentException('Amount of items can not be lower then 1.');
        }

        return $this->client->createUrl(
            '%s?%s',
            [$this->uri,],
            ['start' => $startIndex, 'limit' => $amountOfItems,]
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createGetCustomersUrl(int $organisationId, int $startIndex, int $amountOfItems): string
    {
        if ($startIndex < 0) {
            throw new InvalidArgumentException('Start index can not be lower then 0.');
        }
        if ($amountOfItems < 1) {
            throw new InvalidArgumentException('Amount of items can not be lower then 1.');
        }

        return $this->client->createUrl(
            '%s/%s/user?%s',
            [$this->uri, $organisationId,],
            ['start' => $startIndex, 'limit' => $amountOfItems,]
        );
    }

    /**
     * @throws JsonMapper_Exception
     */
    private function createOrganisation(string $data): Organisation
    {
        return $this->client->map($data, new Organisation());
    }
}