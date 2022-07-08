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
    /**
     * @var ServiceDeskClient|MockObject
     */
    private $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(ServiceDeskClient::class);

        // tmp until updated to version where mapper is exposed
        $this->client->method('mapWithoutDecode')->willReturnCallback(static function (object $jsonData, $dataObject): object {
            return (new JsonMapper())->map($jsonData, $dataObject);
        });
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
    }

    public function testGetParticipant(): void
    {
        $participantService = new ParticipantService($this->client);

        $customerData = [
            'accountId' => '1234',
            'displayName' => 'Foo Bar',
        ];

        $this->client->method('exec')->willReturn(json_encode(['values' => [$customerData]]));

        $result = $participantService->getParticipantOfRequest(123);

        $this->assertEquals([new Customer($customerData)], $result);
    }

    public function testAddParticipant(): void
    {
        $participantService = new ParticipantService($this->client);

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

        $result = $participantService->addParticipantToRequest(123, [new Customer($customerData[1])]);

        $this->assertEquals([new Customer($customerData[0]), new Customer($customerData[1])], $result);
    }

    public function testRemoveParticipant(): void
    {
        $participantService = new ParticipantService($this->client);

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

        $result = $participantService->removeParticipantFromRequest(123, [new Customer(['accountId' => 'la-la'])]);

        $this->assertEquals([new Customer($customerData[0]), new Customer($customerData[1])], $result);
    }
}
