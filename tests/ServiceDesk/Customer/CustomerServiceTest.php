<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Customer;

use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\Customer\CustomerService;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JiraRestApi\User\User;
use JiraRestApi\User\UserService;
use JsonMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomerServiceTest extends TestCase
{
    private ServiceDeskClient|MockObject|null $client;
    private UserService|MockObject|null $userService;
    private ?CustomerService $uut;
    private string $uri = '/customer';

    public function setUp(): void
    {
        $this->client = $this->createMock(ServiceDeskClient::class);
        $this->client->method('getMapper')->willReturn(new JsonMapper());
        $this->userService = $this->createMock(UserService::class);

        $this->uut = new CustomerService($this->client, $this->userService);
    }

    public function tearDown(): void
    {
        $this->client = null;
        $this->userService = null;
        $this->uut = null;
    }

    public function testCreate(): void
    {
        $data = [
            'key' => '123172fwjkdwsf',
            'name' => 'test customer',
            'emailAddress' => 'test@customer.com',
        ];

        $item = json_encode(array_merge($data, ['active' => true]));
        $this->client->method('exec')
            ->with($this->uri, json_encode($data), 'POST')
            ->willReturn($item);

        $result = $this->uut->create($data);

        self::assertSame($data['key'], $result->key);
        self::assertSame($data['name'], $result->name);
        self::assertSame($data['emailAddress'], $result->emailAddress);
        self::assertTrue($result->active);
    }

    public function testCreateFromCustomer(): void
    {
        $customer = $this->createCustomer();

        $item = json_encode($customer);
        $this->client->method('exec')->wilLReturn($item);
        $result = $this->uut->createFromCustomer($customer);

        self::assertSame($customer->name, $result->name);
        self::assertSame($customer->emailAddress, $result->emailAddress);
        self::assertSame($customer->active, $result->active);
        self::assertSame($customer->timeZone, $result->timeZone);
    }

    public function testGet(): void
    {
        $parameters = [
            'key' => '32rwshdfds',
        ];
        $user = $this->createUser('dlskfhsdhg213');

        $this->userService->method('get')->willReturn($user);

        $result = $this->uut->get($parameters);

        self::assertSame($user->key, $result->key);
        self::assertSame($user->emailAddress, $result->emailAddress);
    }

    public function testFindCustomers(): void
    {
        $parameters = [
            'name' => 'Test cu',
        ];
        $users = [
            $this->createUser('kdsfhsdbasdf'),
            $this->createUser('oj217y37gdsaf'),
        ];

        $this->userService->method('findUsers')->willReturn($users);

        $result = $this->uut->findCustomers($parameters);

        self::assertSame($users[0]->key, $result[0]->key);
        self::assertSame($users[0]->emailAddress, $result[0]->emailAddress);
        self::assertSame($users[1]->key, $result[1]->key);
        self::assertSame($users[1]->emailAddress, $result[1]->emailAddress);
    }

    public function testFindCustomersByQuery(): void
    {
        $parameters = [
            'emailAddress' => '%@example.com',
        ];
        $users = [
            $this->createUser('kdsfhsdbasdf'),
            $this->createUser('oj217y37gdsaf'),
        ];

        $this->userService->method('findUsers')->willReturn($users);

        $result = $this->uut->findCustomersByQuery($parameters);

        self::assertSame($users[0]->key, $result[0]->key);
        self::assertSame($users[0]->emailAddress, $result[0]->emailAddress);
        self::assertSame($users[1]->key, $result[1]->key);
        self::assertSame($users[1]->emailAddress, $result[1]->emailAddress);
    }

    public function testGetCustomers(): void
    {
        $parameters = [
            'active' => true,
        ];
        $users = [
            $this->createUser('kdsfhsdbasdf'),
            $this->createUser('oj217y37gdsaf'),
        ];

        $this->userService->method('getUsers')->willReturn($users);

        $result = $this->uut->getCustomers($parameters);

        self::assertSame($users[0]->key, $result[0]->key);
        self::assertSame($users[0]->emailAddress, $result[0]->emailAddress);
        self::assertSame($users[1]->key, $result[1]->key);
        self::assertSame($users[1]->emailAddress, $result[1]->emailAddress);
    }

    private function createCustomer(): Customer
    {
        $customer = new Customer();
        $customer->accountId = '21';
        $customer->name = 'Test Customer';
        $customer->emailAddress = 'test@customer.com';
        $customer->active = true;
        $customer->timeZone = 'UTC';

        return $customer;
    }

    private function createUser(string $key): User
    {
        $user = new User();
        $user->key = $key;
        $user->accountId = '21';
        $user->self = '';
        $user->name = 'Test Customer';
        $user->displayName = 'Test Customer';
        $user->emailAddress = 'test@customer.com';
        $user->active = true;
        $user->timeZone = 'UTC';

        return $user;
    }
}
