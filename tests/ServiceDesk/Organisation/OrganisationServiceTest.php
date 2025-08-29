<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Organisation;

use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\Organisation\Organisation;
use JiraRestApi\ServiceDesk\Organisation\OrganisationService;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrganisationServiceTest extends TestCase
{
    private string $uri = '/servicedeskapi/organization';

    public function testCreate(): void
    {
        $data = [
            'id' => '123',
            'name' => 'test organisation',
        ];

        $item = array_merge(
            $data,
            [
                'links' => ['http://example.com/123'],
            ]
        );

        $client = $this->createClient();
        $client->method('exec')
            ->with($this->uri, json_encode($data), 'POST')
            ->willReturn(json_encode($item));
        $uut = new OrganisationService($client);

        $result = $uut->create($data);

        self::assertSame((int)$data['id'], $result->id);
        self::assertSame($data['name'], $result->name);
        self::assertSame($item['links'], $result->_links);
    }

    public function testCreateFromOrganisation(): void
    {
        $organisation = new Organisation();
        $organisation->name = 'Test organisation';
        $organisation->setLinks(['http://example.com/123']);

        $item = [
            'id' => '136',
            'name' => $organisation->name,
            'links' => $organisation->_links,
        ];

        $client = $this->createClient();
        $client->method('exec')->willReturn(json_encode($item));
        $uut = new OrganisationService($client);

        $result = $uut->createFromOrganisation($organisation);

        self::assertSame((int)$item['id'], $result->id);
        self::assertSame($item['name'], $result->name);
        self::assertSame($item['links'], $result->_links);
    }

    public function testGet(): void
    {
        $id = '623';

        $item = [
            'id' => $id,
            'name' => 'Test organisation',
        ];
        $url = 'https://example.com/organisation/get';

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%s', [$this->uri, $id])
            ->willReturn($url);
        $client->method('exec')
            ->with($url)
            ->willReturn(json_encode($item));
        $uut = new OrganisationService($client);

        $result = $uut->get($id);

        self::assertSame((int)$id, $result->id);
        self::assertSame($item['name'], $result->name);
    }

    public function testGetOrganisations(): void
    {
        $values = [
            [
                'id' => 10,
                'name' => 'Test organisation 1',
            ],
            [
                'id' => 11,
                'name' => 'Test organisation 2',
            ]
        ];
        $url = 'https://example.com/organisation/get';

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s?%s', [$this->uri], ['start' => 1, 'limit' => 20])
            ->willReturn($url);
        $client->method('exec')
            ->with($url)
            ->willReturn(json_encode(['values' => $values]));
        $uut = new OrganisationService($client);

        $result = $uut->getOrganisations(1, 20);

        self::assertCount(2, $result);
        self::assertSame($values[0]['id'], $result[0]->id);
        self::assertSame($values[0]['name'], $result[0]->name);
        self::assertSame($values[1]['id'], $result[1]->id);
        self::assertSame($values[1]['name'], $result[1]->name);
    }

    public function testGetCustomersForOrganisation(): void
    {
        $customers = [
            [
                'key' => 'adeifrhgsadf',
                'name' => 'Customer A',
            ],
            [
                'key' => 'dsfmsd',
                'name' => 'Customer B',
            ],
        ];
        $url = 'https://example.com/organisation/customers/get';

        $organisation = new Organisation();
        $organisation->id = 120;

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%s/user?%s', [$this->uri, $organisation->id], ['start' => 1, 'limit' => 20])
            ->willReturn($url);
        $client->method('exec')
            ->with($url)
            ->willReturn(json_encode($customers));
        $uut = new OrganisationService($client);

        $result = $uut->getCustomersForOrganisation(1, 20, $organisation);

        self::assertCount(2, $result);
        self::assertSame($customers[0]['key'], $result[0]->key);
        self::assertSame($customers[0]['name'], $result[0]->name);
        self::assertSame($customers[1]['key'], $result[1]->key);
        self::assertSame($customers[1]['name'], $result[1]->name);
    }

    public function testAddCustomersToOrganisation(): void
    {
        $customer = new Customer();
        $customer->name = 'Test Customer';

        $organisation = new Organisation();
        $organisation->id = 120;

        $url = 'https://example.com/organisation/customers/add';

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%s', [$this->uri, $organisation->id])
            ->willReturn($url);
        $client->expects($this->once())
            ->method('exec')
            ->with($url, ['usernames' => [$customer->name]], 'POST');
        $uut = new OrganisationService($client);

        $uut->addCustomersToOrganisation([$customer], $organisation);
    }

    public function testDeleteOrganisation(): void
    {
        $organisation = new Organisation();
        $organisation->id = 120;

        $url = 'https://example.com/organisation';

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%s', [$this->uri, $organisation->id])
            ->willReturn($url);
        $client->expects($this->once())
            ->method('exec')
            ->with($url, null, 'DELETE');
        $uut = new OrganisationService($client);

        $uut->deleteOrganisation($organisation);
    }

    /**
     * @return ServiceDeskClient|MockObject
     */
    private function createClient(): MockObject|ServiceDeskClient
    {
        $mapper = new JsonMapper();

        $client = $this->createMock(ServiceDeskClient::class);
        $client->method('getMapper')->willReturn($mapper);

        return $client;
    }
}
