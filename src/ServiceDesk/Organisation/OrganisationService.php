<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Organisation;

use InvalidArgumentException;
use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonException;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;

class OrganisationService
{
    private ServiceDeskClient $client;
    private string $uri = '/servicedeskapi/organization';
    private LoggerInterface $logger;
    private JsonMapper $jsonMapper;

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
        $this->logger = $client->getLogger();
        $this->jsonMapper = $client->getMapper();
    }

    /**
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/organization-createOrganization
     */
    public function create(array|string $data): Organisation
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
        }

        $this->logger->info("Create ServiceDesk Organisation=\n".$data);

        $result = $this->client->exec($this->uri, $data, 'POST');

        return $this->createOrganisation($result);
    }

    /**
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/organization-createOrganization
     */
    public function createFromOrganisation(Organisation $organisation): Organisation
    {
        $data = json_encode($organisation, JSON_THROW_ON_ERROR);

        return $this->create($data);
    }

    /**
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function get(string $organisationId): Organisation
    {
        $result = $this->client->exec(
            $this->client->createUrl('%s/%s', [$this->uri, $organisationId])
        );

        return $this->createOrganisation($result);
    }

    /**
     * Returns the organisations paginated.
     *
     * @throws JiraException|JsonMapper_Exception|JsonException
     *
     * @return Organisation[]
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/organization
     */
    public function getOrganisations(int $startIndex, int $amountOfItems): array
    {
        $result = $this->client->exec(
            $this->createGetOrganisationsUrl($startIndex, $amountOfItems)
        );

        $this->logger->info("Result=\n".$result);

        $organisationData = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        $organisations = [];

        foreach ($organisationData->values as $organisation) {
            $organisations[] = $this->jsonMapper->map($organisation, new Organisation());
        }

        return $organisations;
    }

    /**
     * Returns the organisation customers paginated.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @return Customer[]
     */
    public function getCustomersForOrganisation(int $startIndex, int $amountOfItems, Organisation $organisation): array
    {
        $result = $this->client->exec(
            $this->createGetCustomersUrl($organisation->id, $startIndex, $amountOfItems)
        );

        $this->logger->info("Result=\n".$result);

        $customerData = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        $customers = [];

        foreach ($customerData as $customer) {
            $customers[] = $this->jsonMapper->map(
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
        $customerNames = array_map(static function (Customer $customer) {
            return $customer->name;
        }, $customers);

        $this->client->exec(
            $this->client->createUrl('%s/%s', [$this->uri, $organisation->id]),
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
            $this->client->createUrl('%s/%s', [$this->uri, $organisation->id]),
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
            [$this->uri],
            ['start' => $startIndex, 'limit' => $amountOfItems]
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
            [$this->uri, $organisationId],
            ['start' => $startIndex, 'limit' => $amountOfItems]
        );
    }

    /**
     * @throws JsonMapper_Exception|JsonException
     */
    private function createOrganisation(string $data): Organisation
    {
        return $this->jsonMapper->map(
            json_decode($data, false, 512, JSON_THROW_ON_ERROR),
            new Organisation()
        );
    }
}
