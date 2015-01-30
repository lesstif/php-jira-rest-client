<?php

namespace JiraRestApi;

require 'vendor/autoload.php';

class HTTPException extends \Exception { }

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

	private $options = array(
		// disable SSL Certification validation
		CURLOPT_SSL_VERIFYHOST => 0,
		// FALSE to stop CURL from verifying the peer's certificate. 
		CURLOPT_SSL_VERIFYPEER => 0,

		CURLOPT_VERBOSE => true,

		'LOG_FILE' => 'jira-rest-client.log',
		'LOG_LEVEL' => Logger::INFO,
		);

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

	public function __construct($config, $options = null)
    {
    	$this->json_mapper = new \JsonMapper();
    	$this->json_mapper->bExceptionOnUndefinedProperty = true;

        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];

        if (!is_null($options)) {
        	//http://stackoverflow.com/questions/5929642/php-array-merge-with-numerical-keys
        	// array_merge with numeric key
        	$this->options = $this->options + $options;
        	//$this->options = array_merge($this->options, $options);

        	if (isset($options['LOG_FILE']))
        		$this->options['LOG_FILE'] = $options['LOG_FILE'];
        	if (isset($options['LOG_LEVEL']))
        		$this->options['LOG_LEVEL'] = $this->convertLogLevel($options['LOG_LEVEL']);
        }

        // create logger
        $log_file = $options['LOG_FILE'];
        $log_level =  $this->convertLogLevel($options['LOG_LEVEL']);

        $this->log =  new Logger('JiraClient');
    	$this->log->pushHandler(new StreamHandler($this->options['LOG_FILE'], 
    		$this->options['LOG_LEVEL']));

        $this->http_response = 200;
    }

    public function exec($context, $post_data = null) {
		$url = $this->host . $this->api_uri . '/' . preg_replace('/\//', '', $context, 1);

		$this->log->addDebug("Curl $url JsonData=" . $post_data);	

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);

		// post_data
		if (!is_null($post_data)) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		
		curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");

		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, $this->options[CURLOPT_SSL_VERIFYHOST]);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->options[CURLOPT_SSL_VERIFYPEER]);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array('Accept: */*', 'Content-Type: application/json')); 
		
		curl_setopt($ch, CURLOPT_VERBOSE, $this->options[CURLOPT_VERBOSE]);

		$this->log->addDebug('Curl exec=' . $url);	
		$response = curl_exec($ch);

		// if request failed.
		if (!$response) {		
			$body = curl_error($ch);
			curl_close($ch);
			// HostNotFound, No route to Host, etc Network error
			$this->log->addError("CURL Error: = " . $body);
			throw new HTTPException("CURL Error: = " . $body);
		} else {
			// if request was ok, parsing http response code.
			$this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);

			// don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION 
			if ($this->http_response != 200) {
				throw new HTTPException("CURL HTTP Request Failed: Status Code : "
				 . $this->http_response . " URL:" . $url);
			}			
		}		

		return $response;
	}

	function indent($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

        // If this character is the end of an element,
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}
}




?>