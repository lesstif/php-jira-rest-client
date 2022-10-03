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
    private string $uri = '/organization';
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
     * @see https://docs.atlassian.com/jira-servicedesk/REST/5.2.0/#servicedeskapi/organization-createOrganization
     */
    public function create(string $orgName): Organisation
    {
        $this->logger->info("Create ServiceDesk Organisation=\n".$orgName);

        $data = json_encode(['name' => $orgName]);

        $result = $this->client->exec($this->uri, $data, 'POST');

        return $this->createOrganisation($result);
    }

    /**
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/5.2.0/#servicedeskapi/organization-createOrganization
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
     * @see https://docs.atlassian.com/jira-servicedesk/REST/5.2.0/#servicedeskapi/organization-getOrganizations
     */
    public function getOrganisations(int $start, int $limit): array
    {
        $paramArray = $this->client->toHttpQueryParameter([
            'start' => $start,
            'limit' => $limit,
        ]);

        $response = $this->client->exec($this->uri.$paramArray, null);

        $this->logger->info("Result=\n".$response);

        $organisationData = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        $organisations = [];

        foreach ($organisationData['values'] as $organisation) {
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

        $customerData = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
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
            json_decode($data, true, 512, JSON_THROW_ON_ERROR),
            new Organisation()
        );
    }
}
