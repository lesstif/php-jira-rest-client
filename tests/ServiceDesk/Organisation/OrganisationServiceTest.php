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
    private string $uri = '/organization';

    public function testCreate(): void
    {
        $name = 'test organisation';
        $item = [
            'id' => '123',
            'name' => $name,
            '_links' => ['self' => 'http://example.com/rest/servicedeskapi/organization/123'],
        ];

        $client = $this->createClient();
        $client->method('exec')
            ->with($this->uri, json_encode(['name' => $name]), 'POST')
            ->willReturn(json_encode($item));
        $uut = new OrganisationService($client);

        $result = $uut->create($name);

        self::assertSame((int)$item['id'], $result->id);
        self::assertSame($item['name'], $result->name);
        self::assertSame($item['_links'], $result->_links);
    }

    public function testCreateFromOrganisation(): void
    {
        $organisation = new Organisation();
        $organisation->name = 'Test organisation';

        $item = [
            'id' => '136',
            'name' => $organisation->name,
            '_links' => ['self' => 'http://example.com/rest/servicedeskapi/organization/136'],
        ];

        $client = $this->createClient();
        $client->expects($this->once())
            ->method('exec')
            ->with($this->uri, json_encode(['name' => $organisation->name]), 'POST')
            ->willReturn(json_encode($item));
        $uut = new OrganisationService($client);

        $result = $uut->createFromOrganisation($organisation);

        self::assertSame((int)$item['id'], $result->id);
        self::assertSame($item['name'], $result->name);
    }

    public function testGet(): void
    {
        $id = '623';

        $item = [
            'id' => $id,
            'name' => 'Test organisation',
        ];
        $url = '/organization/623';

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
                'id' => '10',
                'name' => 'Test organisation 1',
            ],
            [
                'id' => '11',
                'name' => 'Test organisation 2',
            ],
        ];
        $url = '/organization?start=1&limit=20';

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s', [$this->uri], ['start' => 1, 'limit' => 20])
            ->willReturn($url);
        $client->method('exec')
            ->with($url)
            ->willReturn(json_encode(['size' => 2, 'start' => 1, 'limit' => 20, 'isLastPage' => true, 'values' => $values]));
        $uut = new OrganisationService($client);

        $result = $uut->getOrganisations(1, 20);

        self::assertCount(2, $result);
        self::assertSame((int)$values[0]['id'], $result[0]->id);
        self::assertSame($values[0]['name'], $result[0]->name);
        self::assertSame((int)$values[1]['id'], $result[1]->id);
        self::assertSame($values[1]['name'], $result[1]->name);
    }

    public function testGetCustomersForOrganisation(): void
    {
        $customers = [
            [
                'key' => 'adeifrhgsadf',
                'name' => 'Customer A',
                'active' => true,
                'timeZone' => 'UTC',
                '_links' => ['jiraRest' => 'http://example.com/rest/api/2/user?username=a', 'avatarUrls' => ['48x48' => 'http://example.com/a.png']],
            ],
            [
                'key' => 'dsfmsd',
                'name' => 'Customer B',
            ],
        ];
        $url = '/organization/120/user?start=1&limit=20';

        $organisation = new Organisation();
        $organisation->id = 120;

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%s/user', [$this->uri, $organisation->id], ['start' => 1, 'limit' => 20])
            ->willReturn($url);
        $client->method('exec')
            ->with($url)
            ->willReturn(json_encode(['size' => 2, 'start' => 1, 'limit' => 20, 'isLastPage' => true, 'values' => $customers]));
        $uut = new OrganisationService($client);

        $result = $uut->getCustomersForOrganisation(1, 20, $organisation);

        self::assertCount(2, $result);
        self::assertSame($customers[0]['key'], $result[0]->key);
        self::assertSame($customers[0]['name'], $result[0]->name);
        self::assertSame($customers[0]['_links']['jiraRest'], $result[0]->_links->jiraRest);
        self::assertSame($customers[1]['key'], $result[1]->key);
        self::assertSame($customers[1]['name'], $result[1]->name);
    }

    public function testAddCustomersToOrganisation(): void
    {
        $customer = new Customer();
        $customer->name = 'Test Customer';

        $organisation = new Organisation();
        $organisation->id = 120;

        $url = '/organization/120/user';

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('%s/%s/user', [$this->uri, $organisation->id])
            ->willReturn($url);
        $client->expects($this->once())
            ->method('exec')
            ->with($url, json_encode(['usernames' => [$customer->name]]), 'POST');
        $uut = new OrganisationService($client);

        $uut->addCustomersToOrganisation([$customer], $organisation);
    }

    public function testDeleteOrganisation(): void
    {
        $organisation = new Organisation();
        $organisation->id = 120;

        $url = '/organization/120';

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
