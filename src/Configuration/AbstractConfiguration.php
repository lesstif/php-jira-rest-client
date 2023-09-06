<?php

namespace JiraRestApi\Configuration;

/**
 * Class AbstractConfiguration.
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    protected ?string $jiraHost;

    protected ?string $jiraUser = null;

    protected ?string $jiraPassword;

    protected bool $jiraLogEnabled;

    protected ?string $jiraLogFile;

    protected ?string $jiraLogLevel;

    /**
     * Curl options CURLOPT_SSL_VERIFYHOST.
     */
    protected bool $curlOptSslVerifyHost;

    /**
     * Curl options CURLOPT_SSL_VERIFYPEER.
     */
    protected bool $curlOptSslVerifyPeer;

    /**
     * Curl option CURLOPT_USERAGENT.
     */
    protected string $curlOptUserAgent;

    /**
     * Curl options CURLOPT_VERBOSE.
     */
    protected bool $curlOptVerbose;

    /**
     * HTTP header 'Authorization: Bearer {token}' for OAuth.
     */
    protected ?string $oauthAccessToken = null;

    /**
     * enable cookie authorization.
     */
    protected bool $cookieAuthEnabled;

    /**
     * HTTP cookie file name.
     */
    protected ?string $cookieFile = null;

    /**
     * Proxy server.
     */
    protected ?string $proxyServer = null;

    /**
     * Proxy port.
     */
    protected ?string $proxyPort = null;

    /**
     * Proxy type.
     */
    protected ?int $proxyType = null;

    /**
     * Proxy user.
     */
    protected ?string $proxyUser = null;

    /**
     * Proxy password.
     */
    protected ?string $proxyPassword = null;

    protected ?string $curlOptSslCert;

    protected ?string $curlOptSslCertPassword;

    protected ?string $curlOptSslKey;

    protected ?string $curlOptSslKeyPassword;

    protected int $timeout = 60;

    protected bool $useTokenBasedAuth;

    protected ?string $personalAccessToken;

    protected ?int $serviceDeskId;

    public function getJiraHost(): string
    {
        return $this->jiraHost;
    }

    public function getJiraUser(): string
    {
        return $this->jiraUser;
    }

    public function getJiraPassword(): string
    {
        return $this->jiraPassword;
    }

    public function getJiraLogEnabled(): bool
    {
        return $this->jiraLogEnabled;
    }

    public function getJiraLogFile(): string
    {
        return $this->jiraLogFile;
    }

    public function getJiraLogLevel(): string
    {
        return $this->jiraLogLevel;
    }

    public function isCurlOptSslVerifyHost(): bool
    {
        return $this->curlOptSslVerifyHost;
    }

    public function isCurlOptSslVerifyPeer(): bool
    {
        return $this->curlOptSslVerifyPeer;
    }

    public function isCurlOptSslCert(): ?string
    {
        return $this->curlOptSslCert;
    }

    public function isCurlOptSslCertPassword(): ?string
    {
        return $this->curlOptSslCertPassword;
    }

    public function isCurlOptSslKey(): ?string
    {
        return $this->curlOptSslKey;
    }

    public function isCurlOptSslKeyPassword(): ?string
    {
        return $this->curlOptSslKeyPassword;
    }

    public function isCurlOptVerbose(): bool
    {
        return $this->curlOptVerbose;
    }

    /**
     * Get curl option CURLOPT_USERAGENT.
     */
    public function getCurlOptUserAgent(): ?string
    {
        return $this->curlOptUserAgent;
    }

    public function getOAuthAccessToken(): string
    {
        return $this->oauthAccessToken;
    }

    public function isCookieAuthorizationEnabled(): bool
    {
        return $this->cookieAuthEnabled;
    }

    /**
     * get default User-Agent String.
     */
    public function getDefaultUserAgentString(): string
    {
        $curlVersion = curl_version();

        return sprintf('curl/%s (%s)', $curlVersion['version'], $curlVersion['host']);
    }

    public function getCookieFile(): ?string
    {
        return $this->cookieFile;
    }

    public function getProxyServer(): ?string
    {
        return $this->proxyServer;
    }

    public function getProxyPort(): ?string
    {
        return $this->proxyPort;
    }

    public function getProxyType(): ?int
    {
        return $this->proxyType;
    }

    public function getProxyUser(): ?string
    {
        return $this->proxyUser;
    }

    public function getProxyPassword(): ?string
    {
        return $this->proxyPassword;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function isTokenBasedAuth(): bool
    {
        return $this->useTokenBasedAuth;
    }

    public function getPersonalAccessToken(): string
    {
        return $this->personalAccessToken;
    }

    public function getServiceDeskId(): ?int
    {
        return $this->serviceDeskId;
    }
}
