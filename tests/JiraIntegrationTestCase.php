<?php

namespace JiraRestApi\Test;

use Dotenv\Exception\ExceptionInterface as DotenvException;
use JiraRestApi\Configuration\DotEnvConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * Base class for tests which need a live Jira server.
 *
 * Tests are skipped when the Jira connection isn't configured (no .env file or JIRA_HOST/TOKEN_BASED_AUTH environment variables).
 *
 * @see https://github.com/lesstif/php-jira-rest-client/issues/532
 */
abstract class JiraIntegrationTestCase extends TestCase
{
    private static ?bool $jiraConfigured = null;

    protected function setUp(): void
    {
        parent::setUp();

        self::$jiraConfigured ??= self::isJiraConfigured();

        if (!self::$jiraConfigured) {
            $this->markTestSkipped('Jira integration test. Copy .env.example to .env and set up your Jira server to run it.');
        }
    }

    private static function isJiraConfigured(): bool
    {
        // same lookup path as JiraClient::__construct()
        $path = file_exists('./.env') ? './' : '../';

        try {
            new DotEnvConfiguration($path);

            return true;
        } catch (DotenvException) {
            return false;
        }
    }
}
