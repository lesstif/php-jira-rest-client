<?php

namespace JiraRestApi\Configuration;

/**
 * Class AbstractConfiguration.
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * Jira host.
     *
     * @var string
     */
    protected $jiraHost;

    /**
     * Jira login.
     *
     * @var string
     */
    protected $jiraUser;

    /**
     * Jira password.
     *
     * @var string
     */
    protected $jiraPassword;

    /**
     * Enabled write to log.
     *
     * @var bool
     */
    protected $jiraLogEnabled;

    /**
     * Path to log file.
     *
     * @var string
     */
    protected $jiraLogFile;

    /**
     * Log level (DEBUG, INFO, ERROR, WARNING).
     *
     * @var string
     */
    protected $jiraLogLevel;

    /**
     * Curl options CURLOPT_SSL_VERIFYHOST.
     *
     * @var bool
     */
    protected $curlOptSslVerifyHost;

    /**
     * Curl options CURLOPT_SSL_VERIFYPEER.
     *
     * @var bool
     */
    protected $curlOptSslVerifyPeer;

    /**
     * Curl option CURLOPT_USERAGENT.
     *
     * @var string
     */
    protected $curlOptUserAgent;

    /**
     * Curl options CURLOPT_VERBOSE.
     *
     * @var bool
     */
    protected $curlOptVerbose;

    /**
     * HTTP header 'Authorization: Bearer {token}' for OAuth.
     *
     * @var string
     */
    protected $oauthAccessToken;

    /**
     * enable cookie authorization.
     *
     * @var bool
     */
    protected $cookieAuthEnabled;

    /**
     * HTTP cookie file name.
     *
     * @var string
     */
    protected $cookieFile;

    /**
     * Proxy server.
     *
     * @var string
     */
    protected $proxyServer;

    /**
     * Proxy port.
     *
     * @var string
     */
    protected $proxyPort;

    /**
     * Proxy user.
     *
     * @var string
     */
    protected $proxyUser;

    /**
     * Proxy password.
     *
     * @var string
     */
    protected $proxyPassword;

    /**
     * Use Jira Cloud REST API v3.
     *
     * @var bool
     */
    protected $useV3RestApi;

    /**
     * @return string
     */
    public function getJiraHost()
    {
        return $this->jiraHost;
    }

    /**
     * @return string
     */
    public function getJiraUser()
    {
        return $this->jiraUser;
    }

    /**
     * @return string
     */
    public function getJiraPassword()
    {
        return $this->jiraPassword;
    }

    /**
     * @return bool
     */
    public function getJiraLogEnabled()
    {
        return $this->jiraLogEnabled;
    }

    /**
     * @return string
     */
    public function getJiraLogFile()
    {
        return $this->jiraLogFile;
    }

    /**
     * @return string
     */
    public function getJiraLogLevel()
    {
        return $this->jiraLogLevel;
    }

    /**
     * @return bool
     */
    public function isCurlOptSslVerifyHost()
    {
        return $this->curlOptSslVerifyHost;
    }

    /**
     * @return bool
     */
    public function isCurlOptSslVerifyPeer()
    {
        return $this->curlOptSslVerifyPeer;
    }

    /**
     * @return bool
     */
    public function isCurlOptVerbose()
    {
        return $this->curlOptVerbose;
    }

    /**
     * Get curl option CURLOPT_USERAGENT.
     *
     * @return string
     */
    public function getCurlOptUserAgent()
    {
        return $this->curlOptUserAgent;
    }

    /**
     * @return string
     */
    public function getOAuthAccessToken()
    {
        return $this->oauthAccessToken;
    }

    /**
     * @return string
     */
    public function isCookieAuthorizationEnabled()
    {
        return $this->cookieAuthEnabled;
    }

    /**
     * get default User-Agent String.
     *
     * @return string
     */
    public function getDefaultUserAgentString()
    {
        $curlVersion = curl_version();

        return sprintf('curl/%s (%s)', $curlVersion['version'], $curlVersion['host']);
    }

    /**
     * @return string
     */
    public function getCookieFile()
    {
        return $this->cookieFile;
    }

    /**
     * @return string
     */
    public function getProxyServer()
    {
        return $this->proxyServer;
    }

    /**
     * @return string
     */
    public function getProxyPort()
    {
        return $this->proxyPort;
    }

    /**
     * @return string
     */
    public function getProxyUser()
    {
        return $this->proxyUser;
    }

    /**
     * @return string
     */
    public function getProxyPassword()
    {
        return $this->proxyPassword;
    }

    /**
     * @return bool
     */
    public function getUseV3RestApi()
    {
        return $this->useV3RestApi;
    }
}
