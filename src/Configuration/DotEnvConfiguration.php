<?php

namespace JiraRestApi\Configuration;

use JiraRestApi\JiraException;

/**
 * Class DotEnvConfiguration.
 */
class DotEnvConfiguration extends AbstractConfiguration
{
    /**
     * @param string $path
     *
     * @throws JiraException
     */
    public function __construct(string $path = '.')
    {
        $this->loadDotEnv($path);

        $this->jiraHost = $this->env('JIRA_HOST');
        $this->jiraUser = $this->env('JIRA_USER');
        $this->jiraPassword = $this->env('JIRA_PASS');
        $this->oauthAccessToken = $this->env('OAUTH_ACCESS_TOKEN');
        $this->cookieAuthEnabled = $this->env('COOKIE_AUTH_ENABLED', false);
        $this->cookieFile = $this->env('COOKIE_FILE', 'jira-cookie.txt');
        $this->jiraLogEnabled = $this->env('JIRA_LOG_ENABLED', true);
        $this->jiraLogFile = $this->env('JIRA_LOG_FILE', 'jira-rest-client.log');
        $this->jiraLogLevel = $this->env('JIRA_LOG_LEVEL', 'WARNING');
        $this->curlOptSslVerifyHost = $this->env('CURLOPT_SSL_VERIFYHOST', false);
        $this->curlOptSslVerifyPeer = $this->env('CURLOPT_SSL_VERIFYPEER', false);
        $this->curlOptSslCert = $this->env('CURLOPT_SSL_CERT');
        $this->curlOptSslCertPassword = $this->env('CURLOPT_SSL_CERT_PASSWORD');
        $this->curlOptSslKey = $this->env('CURLOPT_SSL_KEY');
        $this->curlOptSslKeyPassword = $this->env('CURLOPT_SSL_KEY_PASSWORD');
        $this->curlOptUserAgent = $this->env('CURLOPT_USERAGENT', $this->getDefaultUserAgentString());
        $this->curlOptVerbose = $this->env('CURLOPT_VERBOSE', false);
        $this->proxyServer = $this->env('PROXY_SERVER');
        $this->proxyPort = $this->env('PROXY_PORT');
        $this->proxyType = $this->env('PROXY_TYPE');
        $this->proxyUser = $this->env('PROXY_USER');
        $this->proxyPassword = $this->env('PROXY_PASSWORD');

        $this->timeout = $this->env('JIRA_TIMEOUT', 30);

        $this->useTokenBasedAuth = $this->env('TOKEN_BASED_AUTH', false);
        $this->personalAccessToken = $this->env('PERSONAL_ACCESS_TOKEN', false);
        $this->serviceDeskId = $this->env('JIRA_SERVICE_DESK_ID', null);
    }

    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     */
    private function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return null;
        }

        if ($this->startsWith($value, '"') && $this->endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * Determine if a given string starts with a given substring.
     */
    public function startsWith(string $haystack, array|string $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     */
    public function endsWith(string $haystack, array|string $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * load dotenv.
     */
    private function loadDotEnv(string $path)
    {
        $requireParam = [
            'JIRA_HOST', 'TOKEN_BASED_AUTH',
        ];

        // support for dotenv 1.x and 2.x. see also https://github.com/lesstif/php-jira-rest-client/issues/102
        //if (method_exists('\Dotenv\Dotenv', 'createImmutable')) {    // v4 or above
        $dotenv = \Dotenv\Dotenv::createImmutable($path);

        $dotenv->safeLoad();
        $dotenv->required($requireParam);
        //}
    }
}
