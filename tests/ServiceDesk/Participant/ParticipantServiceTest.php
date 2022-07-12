<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Participant;

use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\Participant\ParticipantService;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ParticipantServiceTest extends TestCase
{
    private ServiceDeskClient|MockObject|null $client;
    private ParticipantService|null $uut;

    public function setUp(): void
    {
        $this->client = $this->createMock(ServiceDeskClient::class);
        $this->client->method('getMapper')->willReturn(new JsonMapper());

        $this->uut = new ParticipantService($this->client);
    }

    public function tearDown(): void
    {
        $this->client = null;
        $this->uut = null;
    }

    public function testGetParticipant(): void
    {
        $customerData = [
            'accountId' => '1234',
            'displayName' => 'Foo Bar',
        ];

        $this->client->method('exec')->willReturn(json_encode(['values' => [$customerData]]));

        $result = $this->uut->getParticipantOfRequest('K-123');

        $this->assertEquals([$this->createCustomer($customerData)], $result);
    }

    public function testAddParticipant(): void
    {
        $customerData = [
            [
                'accountId' => '1234',
                'displayName' => 'Foo Bar',
            ],
            [
                'accountId' => '12345',
                'displayName' => 'Foo Bar2',
            ],
        ];

        $this->client->method('exec')->willReturn(json_encode(['values' => $customerData]));

        $result = $this->uut->addParticipantToRequest('K-123', [$this->createCustomer($customerData[1])]);

        $this->assertEquals([$this->createCustomer($customerData[0]), $this->createCustomer($customerData[1])], $result);
    }

    public function testRemoveParticipant(): void
    {
        $customerData = [
            [
                'accountId' => '1234',
                'displayName' => 'Foo Bar',
            ],
            [
                'accountId' => '12345',
                'displayName' => 'Foo Bar2',
            ],
        ];

        $this->client->method('exec')->willReturn(json_encode(['values' => $customerData]));

        $result = $this->uut->removeParticipantFromRequest('K-123', [$this->createCustomer(['accountId' => 'la-la'])]);

        $this->assertEquals([$this->createCustomer($customerData[0]), $this->createCustomer($customerData[1])], $result);
    }

    private function createCustomer(array $data): Customer
    {
        $customer = new Customer();
        foreach ($data as $key => $value) {
            $customer->$key = $value;
        }

        return $customer;
    }
}
