<?php

namespace JiraRestApi\Configuration;

use JiraRestApi\Interfaces\ConfigurationInterface;

/**
 * Class AbstractConfiguration
 * @package JiraRestApi\Configuration
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
}
