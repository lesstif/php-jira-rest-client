<?php

namespace JiraRestApi\Test;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use PHPUnit\Framework\TestCase;

/**
 * @see https://github.com/lesstif/php-jira-rest-client/issues/363
 */
class RenderWikiMarkupTest extends TestCase
{
    private function createService($response): IssueService
    {
        $config = new ArrayConfiguration([
            'jiraHost'       => 'http://localhost',
            'jiraUser'       => 'user',
            'jiraPassword'   => 'pass',
            'jiraLogEnabled' => false,
        ]);

        return new class($response, $config) extends IssueService {
            public array $requests = [];

            public function __construct(private $response, ArrayConfiguration $config)
            {
                parent::__construct($config);
            }

            public function exec(string $context, array|string|null $post_data = null, ?string $custom_request = null, ?string $cookieFile = null): string|bool
            {
                $this->requests[] = ['url' => $this->createUrlByContext($context), 'data' => $post_data, 'method' => $custom_request];

                if ($this->response instanceof JiraException) {
                    throw $this->response;
                }

                return $this->response;
            }
        };
    }

    /** @test */
    public function render_wiki_markup(): void
    {
        $service = $this->createService('<p><b>bold</b></p>');

        $this->assertSame('<p><b>bold</b></p>', $service->renderWikiMarkup('*bold*', 'TEST-1'));
        $this->assertSame([
            'url'    => 'http://localhost/rest/api/1.0/render',
            'data'   => '{"rendererType":"atlassian-wiki-renderer","unrenderedMarkup":"*bold*","issueKey":"TEST-1"}',
            'method' => 'POST',
        ], $service->requests[0]);
        $this->assertSame('/rest/api/2', $service->getAPIUri());
    }

    /** @test */
    public function api_uri_is_restored_when_request_fails(): void
    {
        $service = $this->createService(new JiraException('Not Found', 404));
        $service->setAPIUri('/rest/api/3');

        try {
            $service->renderWikiMarkup('*bold*');
            $this->fail('JiraException expected');
        } catch (JiraException) {
        }

        $this->assertSame('/rest/api/3', $service->getAPIUri());
    }
}
