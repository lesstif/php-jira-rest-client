<?php

/**
 * interact jira server with REST API
 */
class JiraClient {

	/** @var CURL instrance */
	protected $curl;

	/** @var jira host */
	protected $host;
	/** @var jira username */
	protected $username;
	/** @var jira password */
	protected $password;

	public function __construct($config)
    {
        $this->$host = $config->host;
        $this->$username = $config->username;
        $this->$password = $config->password;        
    }
}




?>