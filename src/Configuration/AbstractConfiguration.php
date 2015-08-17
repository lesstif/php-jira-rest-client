<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 17.08.15
 * Time: 22:23
 */

namespace JiraRestApi\Configuration;

/**
 * Class AbstractConfiguration
 *
 * @package JiraRestApi\Configuration
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * Jira host
     *
     * @var string
     */
    protected $jiraHost;

    /**
     * Jira login
     *
     * @var string
     */
    protected $jiraUser;

    /**
     * Jira password
     *
     * @var string
     */
    protected $jiraPassword;

    /**
     * Path to log file
     *
     * @var string
     */
    protected $jiraLogFile;

    /**
     * Log level (DEBUG, INFO, ERROR, WARNING)
     *
     * @var string
     */
    protected $jiraLogLevel;

    /**
     * Curl options CURLOPT_SSL_VERIFYHOST
     *
     * @var boolean
     */
    protected $curlOptSslVerifyHost;

    /**
     * Curl options CURLOPT_SSL_VERIFYPEER
     *
     * @var boolean
     */
    protected $curlOptSslVerifyPeer;

    /**
     * Curl options CURLOPT_VERBOSE
     *
     * @var boolean
     */
    protected $curlOptVerbose;

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
     * @return boolean
     */
    public function isCurlOptSslVerifyHost()
    {
        return $this->curlOptSslVerifyHost;
    }

    /**
     * @return boolean
     */
    public function isCurlOptSslVerifyPeer()
    {
        return $this->curlOptSslVerifyPeer;
    }

    /**
     * @return boolean
     */
    public function isCurlOptVerbose()
    {
        return $this->curlOptVerbose;
    }
}
