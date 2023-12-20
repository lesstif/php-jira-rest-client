<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk;

use DateTime;
use DateTimeInterface;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\JsonMapperHelper;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ServiceDeskClientTest extends TestCase
{
    private ?ServiceDeskClient $uut;
    private ?LoggerInterface $logger;

    public function setUp(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('getJiraLogEnabled')->willReturn(true);

        $this->logger = $this->createMock(LoggerInterface::class);

        $this->uut = new ServiceDeskClient(
            $configuration,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        $this->uut = null;
        $this->logger = null;
    }

    public function testGetLogger(): void
    {
        $logger = $this->uut->getLogger();

        self::assertSame($this->logger, $logger);
    }

    public function testGetMapper(): void
    {
        $mapper = $this->uut->getMapper();

        self::assertInstanceOf(JsonMapper::class, $mapper);
        self::assertInstanceOf(JsonMapperHelper::class, $mapper->undefinedPropertyHandler[0]);
        self::assertSame('setUndefinedProperty', $mapper->undefinedPropertyHandler[1]);
        self::assertSame(DateTime::class, $mapper->classMap['\\' . DateTimeInterface::class]);
    }

    public function testCreateUrl(): void
    {
        $format = 'https://example.com/api/v%d/user/%s';
        $parameters = [
            1,
            'get',
        ];

        $result = $this->uut->createUrl($format, $parameters);
        $expected = 'https://example.com/api/v1/user/get';

        self::assertSame($expected, $result);
    }

    public function testCreateUrlWithUrlParameters(): void
    {
        $format = 'https://example.com/api/v%d/user/%s';
        $parameters = [
            1,
            'get',
        ];
        $urlParameters = [
            'start' => 1,
            'amount' => 10,
        ];

        $result = $this->uut->createUrl($format, $parameters, $urlParameters);
        $expected = 'https://example.com/api/v1/user/get?start=1&amount=10';

        self::assertSame($expected, $result);
    }
}