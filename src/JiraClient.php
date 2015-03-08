<?php

namespace JiraRestApi;

require 'vendor/autoload.php';

class JIRAException extends \Exception { }

use \Monolog\Logger as Logger;
use \Monolog\Handler\StreamHandler;

use \Noodlehaus\Config as Config;

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

	protected $LOG_FILE;
	protected $LOG_LEVEL;
	
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

	// serilize only not null field.
	protected function filterNullVariable($haystack)
	{
	    foreach ($haystack as $key => $value) {
	        if (is_array($value) ) {
	            $haystack[$key] = $this->filterNullVariable($haystack[$key]);
	        } else if (is_object($value)) {
	        	$haystack[$key] = $this->filterNullVariable(get_class_vars(get_class($value)));
	        }

	        if (is_null($haystack[$key]) || empty($haystack[$key])) {
	            unset($haystack[$key]);
	        }
	    }

	    return $haystack;
	}

	public function __construct()
    {	
    	$config = Config::load('config.jira.json');

    	$this->json_mapper = new \JsonMapper();
    	$this->json_mapper->bExceptionOnUndefinedProperty = true;

        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];

        $this->CURLOPT_SSL_VERIFYHOST = $config->get('CURLOPT_SSL_VERIFYHOST', false);

       	$this->CURLOPT_SSL_VERIFYPEER = $config->get('CURLOPT_SSL_VERIFYPEER', false);
       	$this->CURLOPT_VERBOSE = $config->get('CURLOPT_VERBOSE', false);

        $this->LOG_FILE = $config->get('LOG_FILE', 'jira-rest-client.log');
       	$this->LOG_LEVEL = $this->convertLogLevel($config->get('LOG_LEVEL', Logger::INFO));

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
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}
			if (!is_null($custom_request) && $custom_request == "DELETE") {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
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
			$this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$body = curl_error($ch);
			curl_close($ch);

			//The server successfully processed the request, but is not returning any content. 
			if ($this->http_response == 204){
				return "";
			}
			
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

	/**
	 * file upload
	 * 
	 * @param context url context
	 * @param upload_file upload file.
	 * 
	 * @todo multiple file upload suppport.
	 * 
	 */
	public function upload($context, $upload_file) {
		$url = $this->host . $this->api_uri . '/' . preg_replace('/\//', '', $context, 1);

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);				

		/* CURLFile support PHP 5.5 
		$cf = new \CURLFile(realpath($upload_file), 'image/png', $upload_file);		
		$this->log->addDebug('CURLFile=' . var_export($cf, true));	
		*/
		
		$attachments = realpath($upload_file);
		$filename = basename($upload_file);

        // send file
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
			array('file' => '@' . $attachments . ';filename=' . $filename));
        
		curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");

		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, $this->CURLOPT_SSL_VERIFYHOST);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->CURLOPT_SSL_VERIFYPEER);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				'Accept: */*', 
				'Content-Type: multipart/form-data',
				'X-Atlassian-Token: nocheck'
				)); 
		
		curl_setopt($ch, CURLOPT_VERBOSE, $this->CURLOPT_VERBOSE);

		$this->log->addDebug('Curl exec=' . $url);	
		$response = curl_exec($ch);

		// if request failed.
		if (!$response) {
			$this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$body = curl_error($ch);
			curl_close($ch);

			//The server successfully processed the request, but is not returning any content. 
			if ($this->http_response == 204){
				return "";
			}
			
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