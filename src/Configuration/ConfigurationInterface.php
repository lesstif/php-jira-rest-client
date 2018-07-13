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
     * Curl options CURLOPT_SSLVERSION.
     *
     * @return int|null
     */
    public function getCurlOptSslVersion();

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
}
