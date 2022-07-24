<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Request;

use DateTime;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\Request\Request;
use JiraRestApi\ServiceDesk\Request\RequestStatus;
use PHPUnit\Framework\TestCase;
use stdClass;

class RequestTest extends TestCase
{
    public function testSetRequestTypeId(): void
    {
        $requestTypeId = '10';

        $uut = new Request();
        $uut->setRequestTypeId($requestTypeId);

        self::assertSame($requestTypeId, $uut->requestTypeId);
    }

    public function testSetCreatedDate(): void
    {
        $createdDate = new stdClass();
        $createdDate->iso8601 = '2022/05/10';

        $uut = new Request();
        $uut->setCreatedDate($createdDate);

        self::assertInstanceOf(DateTime::class, $uut->createdDate);
        self::assertSame($createdDate->iso8601, $uut->createdDate->format('Y/m/d'));
    }

    public function testSetReporter(): void
    {
        $reporter = new stdClass();
        $reporter->key = '12334221dsf';
        $reporter->name = 'Test reporter';

        $uut = new Request();
        $uut->setReporter($reporter);

        self::assertInstanceOf(Customer::class, $uut->reporter);
        self::assertSame($reporter->key, $uut->reporter->key);
        self::assertSame($reporter->name, $uut->reporter->name);
    }

    public function testSetSummary(): void
    {
        $summary = 'Test summary';

        $uut = new Request();
        $uut->setSummary($summary);

        self::assertArrayHasKey('summary', $uut->requestFieldValues);
        self::assertSame($summary, $uut->requestFieldValues['summary']);
    }

    public function testSetDescription(): void
    {
        $description = 'Test description';

        $uut = new Request();
        $uut->setDescription($description);

        self::assertArrayHasKey('description', $uut->requestFieldValues);
        self::assertSame($description, $uut->requestFieldValues['description']);
    }

    public function testAddCustomField(): void
    {
        $key = 'Custom key';
        $value = 'Custom value';

        $uut = new Request();
        $uut->addCustomField($key, $value);

        self::assertArrayHasKey($key, $uut->requestFieldValues);
        self::assertSame($value, $uut->requestFieldValues[$key]);
    }

    public function testSetCurrentStatus(): void
    {
        $currentStatus = new stdClass();
        $currentStatus->status = 'Updated';

        $uut = new Request();
        $uut->setCurrentStatus($currentStatus);

        self::assertInstanceOf(RequestStatus::class, $uut->currentStatus);
        self::assertSame($currentStatus->status, $uut->currentStatus->status);
    }

    public function testSetLinks(): void
    {
        $links = new stdClass();
        $links->values = ['Test link'];

        $uut = new Request();
        $uut->setLinks($links);

        self::assertSame($links, $uut->_links);
    }
}