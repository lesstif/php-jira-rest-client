<?php

namespace JiraRestApi\Interfaces;

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

//    /**
//     * Path to log file.
//     *
//     * @return string
//     */
//    public function getJiraLogger();
//
//
//    /**
//     * Path to log file.
//     *
//     * @return string
//     */
//    public function getJiraTransport();

}
