<?php

namespace JiraRestApi\ServiceDesk\Customer;

use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JiraRestApi\User\User;
use JiraRestApi\User\UserService;
use JsonMapper_Exception;
use RuntimeException;

class CustomerService
{
    /**
     * @var ServiceDeskClient
     */
    private $client;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var string
     */
    private $uri = '/customer';

    public function __construct(
        ServiceDeskClient $client,
        UserService $userService
    )
    {
        $this->client = $client;
        $this->userService = $userService;
    }

    /**
     * Creates a new customer.
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/customer
     */
    public function create(array $data): Customer
    {
        $this->client->log("Create Customer=\n".json_encode($data));

        $result = $this->client->exec($this->uri, json_encode($data, JSON_UNESCAPED_UNICODE), 'POST');

        return $this->client->map($result, new Customer());
    }

    /**
     * Creates a new customer.
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/customer
     */
    public function createFromCustomer(Customer $customer): Customer
    {
        $data = json_encode($customer);

        return $this->create($data);
    }

    /**
     * Function to get customer.
     *
     * @param array $parameters Possible values for $paramArray 'username', 'key'.
     *                          "Either the 'username' or the 'key' query parameters need to be provided".
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
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
     * @return Customer[]
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @throws RuntimeException
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
     * @return Customer[]
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @throws RuntimeException
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
     * @return Customer[]
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @throws RuntimeException
     */
    public function getCustomers(array $parameters): array
    {
        return $this->usersToCustomers(
            $this->userService->getUsers($parameters)
        );
    }

    /**
     * @param User[] $users
     * @return Customer[]
     * @throws RuntimeException
     */
    private function usersToCustomers(array $users): array
    {
        $customers = [];

        foreach($users as $user)
        {
            if (!$user instanceof User)
            {
                throw new RuntimeException('Only able to parse User-objects.');
            }

            $customers[] = $this->userToCustomer($user);
        }

        return $customers;
    }

    private function userToCustomer(User $user): Customer
    {
        $customerData = [
            'name' => $user->name,
            'key' => $user->key,
            'emailAddress' => $user->emailAddress,
            'displayName' => $user->displayName,
            'active' => $user->active,
            'timeZone' => $user->timeZone,
            '_links' => $this->avatarUrlsToLinks($user->self, $user->avatarUrls),
            'self' => $user->self,
        ];

        return new Customer($customerData);
    }

    private function avatarUrlsToLinks(?string $url, ?object $avatarUrls): ?CustomerLinks
    {
        if ($avatarUrls === null) {
            return null;
        }

        return new CustomerLinks(
            [
                'jiraRest' => $url,
                'avatarUrls' => $avatarUrls,
            ]
        );
    }
}