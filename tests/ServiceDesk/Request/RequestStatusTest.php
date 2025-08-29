<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Request;

use DateTime;
use JiraRestApi\ServiceDesk\Request\RequestStatus;
use PHPUnit\Framework\TestCase;

class RequestStatusTest extends TestCase
{
    public function testSetStatusDate(): void
    {
        $statusDate = [
            'iso8601' => '2022/11/30',
        ];

        $uut = new RequestStatus();
        $uut->setStatusDate($statusDate);

        self::assertInstanceOf(DateTime::class, $uut->statusDate);
        self::assertSame($statusDate['iso8601'], $uut->statusDate->format('Y/m/d'));
    }
}