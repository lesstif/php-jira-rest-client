<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Customer;

use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JiraRestApi\User\User;
use JiraRestApi\User\UserService;
use JsonException;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;

class CustomerService
{
    private ServiceDeskClient $client;
    private UserService $userService;
    private string $uri = '/customer';
    private LoggerInterface $logger;
    private JsonMapper $jsonMapper;

    public function __construct(
        ServiceDeskClient $client,
        UserService $userService
    ) {
        $this->client = $client;
        $this->userService = $userService;
        $this->logger = $client->getLogger();
        $this->jsonMapper = $client->getMapper();
    }

    /**
     * Creates a new customer.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/customer
     */
    public function create(array|string $data): Customer
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        }

        $this->logger->info("Create Customer=\n".$data);

        $result = $this->client->exec($this->uri, $data, 'POST');

        return $this->jsonMapper->map(
            json_decode($result, false, 512, JSON_THROW_ON_ERROR),
            new Customer()
        );
    }

    /**
     * Creates a new customer.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/customer
     */
    public function createFromCustomer(Customer $customer): Customer
    {
        $data = json_encode($customer, JSON_THROW_ON_ERROR);

        return $this->create($data);
    }

    /**
     * Function to get customer.
     *
     * @param array $parameters Possible values for $paramArray 'username', 'key'.
     *                          "Either the 'username' or the 'key' query parameters need to be provided".
     *
     * @throws JsonMapper_Exception|JiraException
     */
    public function get(array $parameters): Customer
    {
        return $this->userToCustomer(
            $this->userService->get($parameters)
        );
    }

    /**
     * Returns a list of customers that match the search string and/or property.
     *
     * @param array $parameters
     *
     * @throws JsonMapper_Exception|JiraException|RuntimeException
     *
     * @return Customer[]
     */
    public function findCustomers(array $parameters): array
    {
        return $this->usersToCustomers(
            $this->userService->findUsers($parameters)
        );
    }

    /**
     * Returns a list of users that match with a specific query.
     *
     * @throws JsonMapper_Exception|JiraException|RuntimeException
     *
     * @return Customer[]
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v2/#api-rest-api-2-user-search-query-get
     */
    public function findCustomersByQuery(array $parameters): array
    {
        return $this->usersToCustomers(
            $this->userService->findUsers($parameters)
        );
    }

    /**
     * @param array $parameters
     *
     * @throws JsonMapper_Exception|JiraException|RuntimeException
     *
     * @return Customer[]
     */
    public function getCustomers(array $parameters): array
    {
        return $this->usersToCustomers(
            $this->userService->getUsers($parameters)
        );
    }

    /**
     * @param User[] $users
     *
     * @throws RuntimeException
     *
     * @return Customer[]
     */
    private function usersToCustomers(array $users): array
    {
        $customers = [];

        foreach ($users as $user) {
            if (!$user instanceof User) {
                throw new RuntimeException('Only able to parse User-objects.');
            }

            $customers[] = $this->userToCustomer($user);
        }

        return $customers;
    }

    private function userToCustomer(User $user): Customer
    {
        $customer = new Customer();
        $customer->name = $user->name;
        $customer->key = $user->key;
        $customer->accountId = $user->accountId;
        $customer->emailAddress = $user->emailAddress;
        $customer->displayName = $user->displayName;
        $customer->active = $user->active;
        $customer->timeZone = $user->timeZone;
        $customer->setLinks(
            $this->avatarUrlsToLinks($user->self, $user->avatarUrls)
        );
        $customer->self = $user->self;

        return $customer;
    }

    private function avatarUrlsToLinks(?string $url, ?object $avatarUrls): ?CustomerLinks
    {
        if ($avatarUrls === null) {
            return null;
        }

        $customerLinks = new CustomerLinks();
        $customerLinks->jiraRest = $url;
        $customerLinks->avatarUrls = $avatarUrls;

        return $customerLinks;
    }
}
