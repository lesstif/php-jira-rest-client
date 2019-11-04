<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 17.08.15
 * Time: 21:58.
 */

namespace JiraRestApi\Configuration;

/**
 * Interface ConfigurationInterface.
 */
interface ConfigurationInterface
{
    /**
     * Jira host.
     *
     * @return string
     */
    public function getJiraHost();

    /**
     * Jira login.
     *
     * @return string
     */
    public function getJiraUser();

    /**
     * Jira password.
     *
     * @return string
     */
    public function getJiraPassword();

    /**
     * Enabled write to log.
     *
     * @return bool
     */
    public function getJiraLogEnabled();

    /**
     * Path to log file.
     *
     * @return string
     */
    public function getJiraLogFile();

    /**
     * Log level (DEBUG, INFO, ERROR, WARNING).
     *
     * @return string
     */
    public function getJiraLogLevel();

    /**
     * Curl options CURLOPT_SSL_VERIFYHOST.
     *
     * @return bool
     */
    public function isCurlOptSslVerifyHost();

    /**
     * Curl options CURLOPT_SSL_VERIFYPEER.
     *
     * @return bool
     */
    public function isCurlOptSslVerifyPeer();

    /**
     * Curl options CURLOPT_VERBOSE.
     *
     * @return bool
     */
    public function isCurlOptVerbose();

    /**
     * Get curl option CURLOPT_USERAGENT.
     *
     * @return string
     */
    public function getCurlOptUserAgent();

    /**
     * HTTP header 'Authorization: Bearer {token}' for OAuth.
     *
     * @return string
     */
    public function getOAuthAccessToken();

    /**
     * Use cookie authorization. Login with username and password only once, then use session cookie.
     *
     * @return bool
     */
    public function isCookieAuthorizationEnabled();

    /**
     * get HTTP cookie file name.
     *
     * @return mixed
     */
    public function getCookieFile();

    /**
     * Proxy server.
     *
     * @return string
     */
    public function getProxyServer();

    /**
     * Proxy port.
     *
     * @return string
     */
    public function getProxyPort();

    /**
     * Proxy user.
     *
     * @return string
     */
    public function getProxyUser();

    /**
     * Proxy password.
     *
     * @return string
     */
    public function getProxyPassword();

    /**
     * use REST v3 API.
     *
     * @return bool
     */
    public function getUseV3RestApi();
}
