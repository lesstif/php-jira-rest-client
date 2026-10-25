<?php

namespace JiraRestApi\Test;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\JiraClient;
use PHPUnit\Framework\TestCase;

/**
 * @see https://github.com/lesstif/php-jira-rest-client/issues/92
 */
class AuthorizationTest extends TestCase
{
    private function authorizationHeaders(array $configuration): array
    {
        $client = new class(new ArrayConfiguration(array_merge([
            'jiraHost'     => 'http://localhost',
            'jiraUser'     => 'user',
            'jiraPassword' => 'pass',
        ], $configuration))) extends JiraClient {
            public function headers(): array
            {
                $headers = [];
                $this->authorization(curl_init(), $headers);

                return $headers;
            }
        };

        return array_values(array_filter($client->headers(), static function (string $header) {
            return str_starts_with($header, 'Authorization:');
        }));
    }

    /** @test */
    public function oauth_access_token_is_sent_as_bearer_token(): void
    {
        $this->assertSame(
            ['Authorization: Bearer oauth-token'],
            $this->authorizationHeaders(['oauthAccessToken' => 'oauth-token'])
        );
    }

    /** @test */
    public function oauth_access_token_takes_precedence_over_personal_access_token(): void
    {
        $this->assertSame(
            ['Authorization: Bearer oauth-token'],
            $this->authorizationHeaders([
                'oauthAccessToken'    => 'oauth-token',
                'useTokenBasedAuth'   => true,
                'personalAccessToken' => 'personal-token',
            ])
        );
    }

    /** @test */
    public function personal_access_token_is_used_without_oauth_access_token(): void
    {
        $this->assertSame(
            ['Authorization: Bearer personal-token'],
            $this->authorizationHeaders([
                'useTokenBasedAuth'   => true,
                'personalAccessToken' => 'personal-token',
            ])
        );
    }

    /** @test */
    public function basic_auth_is_used_without_tokens(): void
    {
        // user/password are set with CURLOPT_USERPWD, not with a header.
        $this->assertSame([], $this->authorizationHeaders([]));
        $this->assertSame([], $this->authorizationHeaders(['oauthAccessToken' => '']));
    }
}
