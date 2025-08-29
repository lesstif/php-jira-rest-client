<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Customer;

use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\Customer\CustomerLinks;
use PHPUnit\Framework\TestCase;
use stdClass;

class CustomerTest extends TestCase
{
    public function testSetLinksArray(): void
    {
        $links = new stdClass();
        $links->jiraRest = 'http://example.com';
        $links->avatarUrls = new stdClass();

        $uut = new Customer();
        $uut->setLinks($links);

        self::assertInstanceOf(CustomerLinks::class, $uut->_links);
        self::assertSame($links->jiraRest, $uut->_links->jiraRest);
        self::assertSame($links->avatarUrls, $uut->_links->avatarUrls);
    }

    public function testSetLinks(): void
    {
        $customerLinks = new CustomerLinks();
        $customerLinks->jiraRest = 'http://example.com';
        $customerLinks->avatarUrls = new stdClass();

        $uut = new Customer();
        $uut->setLinks($customerLinks);

        self::assertSame($customerLinks, $uut->_links);
    }
}
