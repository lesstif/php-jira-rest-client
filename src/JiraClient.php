<?php

namespace JiraRestApi;

require 'vendor/autoload.php';

class JIRAException extends \Exception { }

use \Monolog\Logger as Logger;
use \Monolog\Handler\StreamHandler;

/**
 * interact jira server with REST API
 */
class JiraClient {
	/* @var json mapper */
	protected $json_mapper;

	/** @var HTTP response code */
	protected $http_response;

	/** @var JIRA REST API URI */
	private $api_uri = '/rest/api/2';

	/** @var CURL instrance */
	protected $curl;

	/** @var jira host */
	protected $host;
	/** @var jira username */
	protected $username;
	/** @var jira password */
	protected $password;

	/** @var Monolog instance */
	protected $log;

	// disable SSL Certification validation
	protected $CURLOPT_SSL_VERIFYHOST = false;
	// FALSE to stop CURL from verifying the peer's certificate. 
	protected $CURLOPT_SSL_VERIFYPEER = false;

	// debug curl
	protected $CURLOPT_VERBOSE = false;

	protected $LOG_FILE = 'jira-rest-client.log';
	protected $LOG_LEVEL = Logger::INFO;
	
	private function convertLogLevel($log_level) {
		if ($log_level == 'DEBUG')
			return Logger::DEBUG;
		else if ($log_level == 'WARNING')
			return Logger::WARNING;
		else if ($log_level == 'ERROR')
			return Logger::ERROR;
		else
			return Logger::INFO;
	}

	public function __construct($config)
    {
    	$this->json_mapper = new \JsonMapper();
    	$this->json_mapper->bExceptionOnUndefinedProperty = true;

        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];

        if (isset($config['CURLOPT_SSL_VERIFYHOST']))
        	$this->CURLOPT_SSL_VERIFYHOST = $config['CURLOPT_SSL_VERIFYHOST'] === 'true'? true: false;

        if (isset($config['CURLOPT_SSL_VERIFYPEER']))
        	$this->CURLOPT_SSL_VERIFYPEER = $config['CURLOPT_SSL_VERIFYPEER'] === 'true'? true: false;

        if (isset($config['CURLOPT_VERBOSE']))
        	$this->CURLOPT_VERBOSE = $config['CURLOPT_VERBOSE'] === 'true'? true: false;

        if (isset($config['LOG_FILE']))
        	$this->LOG_FILE = $config['LOG_FILE'];

        if (isset($config['LOG_LEVEL']))
        	$this->LOG_LEVEL = $this->convertLogLevel($config['LOG_LEVEL']);

        // create logger      
        $this->log =  new Logger('JiraClient');
    	$this->log->pushHandler(new StreamHandler($this->LOG_FILE, 
    		$this->LOG_LEVEL));

        $this->http_response = 200;
    }

    public function exec($context, $post_data = null, $custom_request = null) {
		$url = $this->host . $this->api_uri . '/' . preg_replace('/\//', '', $context, 1);

		$this->log->addDebug("Curl $url JsonData=" . $post_data);	

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);

		// post_data
		if (!is_null($post_data)) {
			// PUT REQUEST
			if (!is_null($custom_request) && $custom_request == "PUT") {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}
			if (!is_null($custom_request) && $custom_request == "DELETE") {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
			}
			else {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}			
		}
        
		curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");

		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, $this->CURLOPT_SSL_VERIFYHOST);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->CURLOPT_SSL_VERIFYPEER);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array('Accept: */*', 'Content-Type: application/json')); 
		
		curl_setopt($ch, CURLOPT_VERBOSE, $this->CURLOPT_VERBOSE);

		$this->log->addDebug('Curl exec=' . $url);	
		$response = curl_exec($ch);

		// if request failed.
		if (!$response) {		
			$body = curl_error($ch);
			curl_close($ch);
			// HostNotFound, No route to Host, etc Network error
			$this->log->addError("CURL Error: = " . $body);
			throw new JIRAException("CURL Error: = " . $body);
		} else {
			// if request was ok, parsing http response code.
			$this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);

			// don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION 
			if ($this->http_response != 200 && $this->http_response != 201) {
				throw new JIRAException("CURL HTTP Request Failed: Status Code : "
				 . $this->http_response . ", URL:" . $url
				 . "\nError Message : " . $response, $this->http_response);
			}			
		}		

		return $response;
	}

}




?>