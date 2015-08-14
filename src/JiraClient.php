<?php
namespace JiraRestApi;

use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
use Dotenv;

/**
 * Interact jira server with REST API.
 */
class JiraClient
{
    /**
     * Json Mapper
     *
     * @var \JsonMapper
     */
    protected $json_mapper;

    /**
     * HTTP response code
     *
     * @var string
     */
    protected $http_response;

    /**
     * JIRA REST API URI
     *
     * @var string
     */
    private $api_uri = '/rest/api/2';

    /**
     * CURL instance
     *
     * @var resource
     */
    protected $curl;

    /**
     * Jira host
     *
     * @var string
     */
    protected $host;

    /**
     * Jira username
     *
     * @var string
     */
    protected $username;

    /**
     * Jira password
     *
     * @var string
     */
    protected $password;

    /**
     * Monolog instance
     *
     * @var \Monolog\Logger
     */
    protected $log;

    /**
     * Disable SSL Certification validation
     *
     * @var bool
     */
    protected $CURLOPT_SSL_VERIFYHOST = false;

    /**
     * FALSE to stop CURL from verifying the peer's certificate.
     *
     * @var bool
     */
    protected $CURLOPT_SSL_VERIFYPEER = false;

    /**
     * debug curl
     *
     * @var bool
     */
    protected $CURLOPT_VERBOSE = false;

    /**
     * Log filename
     *
     * @var string
     */
    protected $LOG_FILE;

    /**
     * Log level
     *
     * @var int
     */
    protected $LOG_LEVEL;

    /**
     * Constructor
     *
     * @param string $path Path with dotenv file
     */
    public function __construct($path = '.')
    {
        Dotenv::load($path);

        // not available in dotenv 1.1
        // $dotenv->required(['JIRA_HOST', 'JIRA_USER', 'JIRA_PASS']);

        $this->json_mapper = new \JsonMapper();

        $this->host = $this->env('JIRA_HOST');
        $this->username = $this->env('JIRA_USER');
        $this->password = $this->env('JIRA_PASS');

        $this->CURLOPT_SSL_VERIFYHOST = $this->env('CURLOPT_SSL_VERIFYHOST', false);

        $this->CURLOPT_SSL_VERIFYPEER = $this->env('CURLOPT_SSL_VERIFYPEER', false);
        $this->CURLOPT_VERBOSE = $this->env('CURLOPT_VERBOSE', false);

        $this->LOG_FILE = $this->env('JIRA_LOG_FILE', 'jira-rest-client.log');
        $this->LOG_LEVEL = $this->convertLogLevel($this->env('JIRA_LOG_LEVEL', 'WARNING'));

        // create logger
        $this->log = new Logger('JiraClient');
        $this->log->pushHandler(new StreamHandler($this->LOG_FILE,
            $this->LOG_LEVEL));

        $this->http_response = 200;
    }

    /**
     * Convert log level
     *
     * @param $log_level
     *
     * @return int
     */
    private function convertLogLevel($log_level)
    {
        switch ($log_level) {
            case 'DEBUG':
                return Logger::DEBUG;
            case 'INFO':
                return Logger::INFO;
            case 'ERROR':
                return Logger::ERROR;
            default:
                return Logger::WARNING;
        }
    }

    /**
     * Serilize only not null field
     *
     * @param array $haystack
     *
     * @return array
     */
    protected function filterNullVariable($haystack)
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = $this->filterNullVariable($haystack[$key]);
            } elseif (is_object($value)) {
                $haystack[$key] = $this->filterNullVariable(get_class_vars(get_class($value)));
            }

            if (is_null($haystack[$key]) || empty($haystack[$key])) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }

    /**
     * Execute REST request
     *
     * @param string $context Rest API context (ex.:issue, search, etc..)
     * @param null $post_data
     * @param null $custom_request
     *
     * @return string
     * @throws JIRAException
     */
    public function exec($context, $post_data = null, $custom_request = null)
    {
        $url = $this->host.$this->api_uri.'/'.preg_replace('/\//', '', $context, 1);

        $this->log->addDebug("Curl $url JsonData=".$post_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // post_data
        if (!is_null($post_data)) {
            // PUT REQUEST
            if (!is_null($custom_request) && $custom_request == 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
            if (!is_null($custom_request) && $custom_request == 'DELETE') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            } else {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }

        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->CURLOPT_SSL_VERIFYHOST);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->CURLOPT_SSL_VERIFYPEER);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Accept: */*', 'Content-Type: application/json'));

        curl_setopt($ch, CURLOPT_VERBOSE, $this->CURLOPT_VERBOSE);

        $this->log->addDebug('Curl exec='.$url);
        $response = curl_exec($ch);

        // if request failed.
        if (!$response) {
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body = curl_error($ch);
            curl_close($ch);

            //The server successfully processed the request, but is not returning any content.
            if ($this->http_response == 204) {
                return '';
            }

            // HostNotFound, No route to Host, etc Network error
            $this->log->addError('CURL Error: = '.$body);
            throw new JiraException('CURL Error: = '.$body);
        } else {
            // if request was ok, parsing http response code.
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
            if ($this->http_response != 200 && $this->http_response != 201) {
                throw new JiraException('CURL HTTP Request Failed: Status Code : '
                 .$this->http_response.', URL:'.$url
                 ."\nError Message : ".$response, $this->http_response);
            }
        }

        return $response;
    }

    /**
     * Create upload handle
     *
     * @param string $url Request URL
     * @param string $upload_file Filename
     *
     * @return resource
     */
    private function createUploadHandle($url, $upload_file)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // send file
        curl_setopt($ch, CURLOPT_POST, true);

        if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION  < 5) {
            $attachments = realpath($upload_file);
            $filename = basename($upload_file);

            curl_setopt($ch, CURLOPT_POSTFIELDS,
                array('file' => '@'.$attachments.';filename='.$filename));

            $this->log->addDebug('using legacy file upload');
        } else {
            // CURLFile require PHP > 5.5
            $attachments = new \CURLFile(realpath($upload_file));
            $attachments->setPostFilename(basename($upload_file));

            curl_setopt($ch, CURLOPT_POSTFIELDS,
                    array('file' => $attachments));

            $this->log->addDebug('using CURLFile='.var_export($attachments, true));
        }

        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->CURLOPT_SSL_VERIFYHOST);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->CURLOPT_SSL_VERIFYPEER);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Accept: */*',
                'Content-Type: multipart/form-data',
                'X-Atlassian-Token: nocheck',
                ));

        curl_setopt($ch, CURLOPT_VERBOSE, $this->CURLOPT_VERBOSE);

        $this->log->addDebug('Curl exec='.$url);

        return $ch;
    }

    /**
     * File upload
     *
     * @param string $context       url context
     * @param array  $filePathArray upload file path.
     *
     * @return array
     * @throws JiraException
     */
    public function upload($context, $filePathArray)
    {
        $url = $this->host.$this->api_uri.'/'.preg_replace('/\//', '', $context, 1);

        // return value
        $result_code = 200;

        $chArr = array();
        $results = array();
        $mh = curl_multi_init();

        for ($idx = 0; $idx < count($filePathArray); $idx++) {
            $file = $filePathArray[$idx];
            if (file_exists($file) == false) {
                $body = "File $file not found";
                $result_code = -1;
                goto end;
            }
            $chArr[$idx] = $this->createUploadHandle($url, $filePathArray[$idx]);

            curl_multi_add_handle($mh, $chArr[$idx]);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

         // Get content and remove handles.
        for ($idx = 0; $idx < count($chArr); $idx++) {
            $ch = $chArr[$idx];

            $results[$idx] = curl_multi_getcontent($ch);

            // if request failed.
            if (!$results[$idx]) {
                $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $body = curl_error($ch);

                //The server successfully processed the request, but is not returning any content.
                if ($this->http_response == 204) {
                    continue;
                }

                // HostNotFound, No route to Host, etc Network error
                $result_code = -1;
                $body = 'CURL Error: = '.$body;
                $this->log->addError($body);
            } else {
                // if request was ok, parsing http response code.
                $result_code = $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
                if ($this->http_response != 200 && $this->http_response != 201) {
                    $body = 'CURL HTTP Request Failed: Status Code : '
                     .$this->http_response.', URL:'.$url
                     ."\nError Message : ".$response; // @TODO undefined variable $response
                    $this->log->addError($body);
                }
            }
        }

        // clean up
end:
        foreach ($chArr as $ch) {
            $this->log->addDebug('CURL Close handle..');
            curl_close($ch);
            curl_multi_remove_handle($mh, $ch);
        }
        $this->log->addDebug('CURL Multi Close handle..');
        curl_multi_close($mh);
        if ($result_code != 200) {
            throw new JIRAException('CURL Error: = '.$body, $result_code);
        }

        return $results;
    }

    // excerpt from laravel core.

    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    private function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if ($this->startsWith($value, '"') && endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }
}
