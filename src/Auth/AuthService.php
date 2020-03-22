<?php

namespace JiraRestApi\Auth;

use JiraRestApi\Configuration\ConfigurationInterface;
use Psr\Log\LoggerInterface;

class AuthService extends \JiraRestApi\JiraClient
{
    private $auth_api_uri = '/rest/auth/1';

    private $uri = 'session';

    /**
     * @var string
     */
    private $_sessionCookieName;

    /**
     * @var string
     */
    private $_sessionCookieValue;

    /**
     * @var bool
     */
    private $_authInProgress;

    public function isAuthorized()
    {
        return !empty($this->_sessionCookieName) && !empty($this->_sessionCookieValue);
    }

    /**
     * used to prevent infinite recursion when cookie authorization requested and performed.
     *
     * @return bool
     */
    public function isAuthInProgress()
    {
        return $this->_authInProgress;
    }

    /**
     * For internal usage. Performs login and saves session information for far cookie session authorization.
     *
     * @param null|string $username
     * @param null|string $password
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    public function authorizeWithCookie($username = null, $password = null)
    {
        $this->_authInProgress = true;
        $session = $this->login($username, $password);

        $this->_sessionCookieName = $session->session->name;
        $this->_sessionCookieValue = $session->session->value;
        $this->_authInProgress = false;
    }

    public function getSessionCookieName()
    {
        return $this->_sessionCookieName;
    }

    public function getSessionCookieValue()
    {
        return $this->_sessionCookieValue;
    }

    /**
     * AuthService constructor.
     *
     * @param ConfigurationInterface|null   $configuration
     * @param \Psr\Log\LoggerInterface|null $logger
     * @param string                        $path
     *
     * @throws \Exception
     * @throws \JiraRestApi\JiraException
     */
    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setAPIUri($this->auth_api_uri);
    }

    /**
     * Returns information about the currently authenticated user's session.
     * If the caller is not authenticated they will get a 401 Unauthorized status code.
     *
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#auth/1/session-currentUser Jira Reference
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return CurrentUser|object
     */
    public function getCurrentUser()
    {
        $ret = $this->exec($this->uri);

        $user = $this->json_mapper->map(
            json_decode($ret),
            new CurrentUser()
        );

        return $user;
    }

    /**
     * Logs the current user out of JIRA, destroying the existing session, if any.
     *
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#auth/1/session-logout Jira Reference
     *
     * @throws \JiraRestApi\JiraException
     * @throws \Exception
     *
     * @return bool
     */
    public function logout()
    {
        $this->exec($this->uri, '', 'DELETE');

        $this->_sessionCookieName = null;
        $this->_sessionCookieValue = null;

        return true;
    }

    /**
     * Logs the current user out of JIRA, destroying the existing session, if any.
     *
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#auth/1/session-logout Jira Reference
     *
     * @param string|null $username If null - takes username from configuration.
     * @param string|null $password If null - takes password from configuration.
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return AuthSession|object
     */
    public function login($username = null, $password = null)
    {
        if (!$username) {
            $username = $this->getConfiguration()->getJiraUser();
        }

        if (!$password) {
            $password = $this->getConfiguration()->getJiraPassword();
        }

        $ret = $this->exec($this->uri, json_encode([
            'username' => $username,
            'password' => $password,
        ]), 'POST');

        $session = $this->json_mapper->map(
            json_decode($ret),
            new AuthSession()
        );

        return $session;
    }

    /**
     * This method invalidates the any current WebSudo session.
     *
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#auth/1/websudo-release Jira Reference
     *
     * @throws \JiraRestApi\JiraException
     *
     * @return bool
     */
    public function release()
    {
        $this->exec('websudo', '', 'DELETE');

        return true;
    }
}
