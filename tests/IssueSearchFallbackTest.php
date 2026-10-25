<?php

namespace JiraRestApi\Test;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use PHPUnit\Framework\TestCase;

/**
 * search() must work on both Jira Cloud (/search/jql) and Server/Data Center (/search).
 *
 * @see https://github.com/lesstif/php-jira-rest-client/issues/552
 */
class IssueSearchFallbackTest extends TestCase
{
    private function createService(array $responses): IssueService
    {
        $config = new ArrayConfiguration([
            'jiraHost'     => 'http://localhost',
            'jiraUser'     => 'user',
            'jiraPassword' => 'pass',
        ]);

        return new class($responses, $config) extends IssueService {
            public array $requests = [];

            public function __construct(private array $responses, ArrayConfiguration $config)
            {
                parent::__construct($config);
            }

            public function exec(string $context, array|string|null $post_data = null, ?string $custom_request = null, ?string $cookieFile = null): string|bool
            {
                $this->requests[] = ['context' => $context, 'data' => json_decode($post_data, true)];

                $response = array_shift($this->responses);
                if ($response instanceof JiraException) {
                    throw $response;
                }

                return $response;
            }
        };
    }

    /** @test */
    public function search_uses_enhanced_search_api_on_cloud(): void
    {
        $service = $this->createService([
            json_encode(['issues' => [], 'nextPageToken' => 'abc']),
        ]);

        $ret = $service->search('project = TEST', '', 10);

        $this->assertInstanceOf(IssueSearchResult::class, $ret);
        $this->assertSame('abc', $ret->getNextPageToken());
        $this->assertCount(1, $service->requests);
        $this->assertSame('/search/jql', $service->requests[0]['context']);
    }

    /** @test */
    public function search_falls_back_to_legacy_search_on_data_center(): void
    {
        $service = $this->createService([
            new JiraException('Not Found', 404),
            json_encode(['startAt' => 2, 'maxResults' => 2, 'total' => 5, 'issues' => [['key' => 'TEST-3'], ['key' => 'TEST-4']]]),
        ]);

        $ret = $service->search('project = TEST', '2', 2, ['summary'], 'names,schema');

        $this->assertSame('/search', $service->requests[1]['context']);
        $this->assertSame([
            'jql'        => 'project = TEST',
            'startAt'    => 2,
            'maxResults' => 2,
            'fields'     => ['summary'],
            'expand'     => ['names', 'schema'],
        ], $service->requests[1]['data']);
        $this->assertSame('4', $ret->getNextPageToken());
    }

    /** @test */
    public function legacy_search_has_no_next_page_token_on_last_page(): void
    {
        $service = $this->createService([
            new JiraException('Not Found', 404),
            json_encode(['startAt' => 4, 'maxResults' => 2, 'total' => 5, 'issues' => [['key' => 'TEST-5']]]),
        ]);

        $ret = $service->search('project = TEST', '4', 2);

        $this->assertNull($ret->getNextPageToken());
    }

    /** @test */
    public function search_does_not_fall_back_on_other_errors(): void
    {
        $service = $this->createService([
            new JiraException('Bad Request', 400),
        ]);

        $this->expectException(JiraException::class);
        $this->expectExceptionCode(400);

        try {
            $service->search('invalid jql');
        } finally {
            $this->assertCount(1, $service->requests);
        }
    }
}
