<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 17.08.15
 * Time: 22:40.
 */

namespace JiraRestApi\Configuration;

/**
 * Class ArrayConfiguration.
 */
class ArrayConfiguration extends AbstractConfiguration
{
    /**
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->jiraLogEnabled = true;
        $this->jiraLogFile = 'jira-rest-client.log';
        $this->jiraLogLevel = 'WARNING';
        $this->curlOptSslVerifyHost = false;
        $this->curlOptSslVerifyPeer = false;
        $this->curlOptVerbose = false;
        $this->cookieAuthEnabled = false;
        $this->cookieFile = 'jira-cookie.txt';
        $this->curlOptUserAgent = $this->getDefaultUserAgentString();

        $this->useV3RestApi = false;

        foreach ($configuration as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
