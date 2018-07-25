<?php

namespace JiraRestApi;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Configuration\DotEnvConfiguration;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Logger;

/**
 * Interact jira server with REST API.
 */
class JiraClient
{
    /**
     * Json Mapper.
     *
     * @var \JsonMapper
     */
    protected $json_mapper;
    /**
     * HTTP response code.
     *
     * @var string
     */
    protected $http_response;
    /**
     * JIRA REST API URI.
     *
     * @var string
     */
    private $api_uri = '/rest/api/2';
    /**
     * CURL instance.
     *
     * @var resource
     */
    protected $curl;
    /**
     * Monolog instance.
     *
     * @var \Monolog\Logger
     */
    protected $log;
    /**
     * Jira Rest API Configuration.
     *
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * cookie file name.
     *
     * @var string
     */
    protected $cookie = 'jira-cookies.txt';

    /**
     * Constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param Logger                 $logger
     * @param string                 $path
     *
     * @throws JiraException
     * @throws \Exception
     */
    public function __construct(ConfigurationInterface $configuration = null, Logger $logger = null, $path = './')
    {
        if ($configuration === null) {
            if (!file_exists($path . '.env')) {
                // If calling the getcwd() on laravel it will returning the 'public' directory.
                $path = '../';
            }
            $configuration = new DotEnvConfiguration($path);
        }

        $this->configuration = $configuration;
        $this->json_mapper   = new \JsonMapper();

        // Fix "\JiraRestApi\JsonMapperHelper::class" syntax error, unexpected 'class' (T_CLASS), expecting identifier (T_STRING) or variable (T_VARIABLE) or '{' or '$'
        $this->json_mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];

        // create logger
        if ($logger) {
            $this->log = $logger;
        } else {
            $this->log = new Logger('JiraClient');
            $this->log->pushHandler(new StreamHandler(
                $configuration->getJiraLogFile(),
                $this->convertLogLevel($configuration->getJiraLogLevel())
            ));
        }

        $this->http_response = 200;
    }

    /**
     * Convert log level.
     *
     * @param $log_level
     *
     * @return int
     */
    private function convertLogLevel($log_level)
    {
        $log_level = strtoupper($log_level);

        switch ($log_level) {
            case 'EMERGENCY':
                return Logger::EMERGENCY;
            case 'ALERT':
                return Logger::ALERT;
            case 'CRITICAL':
                return Logger::CRITICAL;
            case 'ERROR':
                return Logger::ERROR;
            case 'WARNING':
                return Logger::WARNING;
            case 'NOTICE':
                return Logger::NOTICE;
            case 'DEBUG':
                return Logger::DEBUG;
            case 'INFO':
                return Logger::INFO;
            default:
                return Logger::WARNING;
        }
    }

    /**
     * Serilize only not null field.
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
     * Execute REST request.
     *
     * @param string $context        Rest API context (ex.:issue, search, etc..)
     * @param string $post_data
     * @param string $custom_request [PUT|DELETE]
     *
     * @throws JiraException
     *
     * @return string
     */
    public function exec($context, $post_data = null, $custom_request = null)
    {
        $url = $this->createUrlByContext($context);

        $this->log->addInfo("Curl $custom_request: $url JsonData=" . $post_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // Allow manual setting of the SSL version

        $sslVersion = $this->getConfiguration()->getCurlOptSslVersion();

        if ($sslVersion !== null) {
            curl_setopt($ch, CURLOPT_SSLVERSION, $sslVersion);
        }

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
        } else {
            if (!is_null($custom_request) && $custom_request == 'DELETE') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            } else {
                // This is a GET request, check if we have a cached version of it

                $cacheFilename = sys_get_temp_dir() . "/" . hash('sha256', $url) . ".cache";

                if (file_exists($cacheFilename) && is_readable($cacheFilename)) {
                    // Check the age of the cached result, if its too old we will ignore it

                    if (time() - filemtime($cacheFilename) < 600) {
                        return unserialize(file_get_contents($cacheFilename));
                    } else {
                        // Cache has expired
                        unlink($cacheFilename);
                    }
                }
            }
        }

        $this->authorization($ch);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());

        // curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER,
            ['Accept: */*', 'Content-Type: application/json', 'X-Atlassian-Token: no-check']);

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        $this->log->addDebug('Curl exec=' . $url);
        $response = curl_exec($ch);

        // if request failed.
        if (!$response) {
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body                = curl_error($ch);
            curl_close($ch);

            /*
             * 201: The request has been fulfilled, resulting in the creation of a new resource.
             * 204: The server successfully processed the request, but is not returning any content.
             */
            if ($this->http_response === 204 || $this->http_response === 201) {
                return true;
            }

            // HostNotFound, No route to Host, etc Network error
            $msg = sprintf('CURL Error: http response=%d, %s', $this->http_response, $body);

            $this->log->addError($msg);

            throw new JiraException($msg);
        } else {
            // if request was ok, parsing http response code.
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
            if ($this->http_response != 200 && $this->http_response != 201) {
                throw new JiraException('CURL HTTP Request Failed: Status Code : '
                                        . $this->http_response . ', URL:' . $url
                                        . "\nError Message : " . $response, $this->http_response);
            } else {
                if (isset($cacheFilename)) {
                    file_put_contents($cacheFilename, serialize($response));
                }
            }
        }

        return $response;
    }

    /**
     * Create upload handle.
     *
     * @param string $url         Request URL
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

        if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5) {
            $attachments = realpath($upload_file);
            $filename    = basename($upload_file);

            curl_setopt($ch, CURLOPT_POSTFIELDS,
                ['file' => '@' . $attachments . ';filename=' . $filename]);

            $this->log->addDebug('using legacy file upload');
        } else {
            // CURLFile require PHP > 5.5
            $attachments = new \CURLFile(realpath($upload_file));
            $attachments->setPostFilename(basename($upload_file));

            curl_setopt($ch, CURLOPT_POSTFIELDS,
                ['file' => $attachments]);

            $this->log->addDebug('using CURLFile=' . var_export($attachments, true));
        }

        $this->authorization($ch);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());

        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: */*',
            'Content-Type: multipart/form-data',
            'X-Atlassian-Token: nocheck',
        ]);

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        $this->log->addDebug('Curl exec=' . $url);

        return $ch;
    }

    /**
     * File upload.
     *
     * @param string $context       url context
     * @param array  $filePathArray upload file path.
     *
     * @throws JiraException
     *
     * @return array
     */
    public function upload($context, $filePathArray)
    {
        $url = $this->createUrlByContext($context);

        // return value
        $result_code = 200;

        $chArr   = [];
        $results = [];
        $mh      = curl_multi_init();

        for ($idx = 0; $idx < count($filePathArray); $idx++) {
            $file = $filePathArray[$idx];
            if (file_exists($file) == false) {
                $body        = "File $file not found";
                $result_code = -1;
                $this->closeCURLHandle($chArr, $mh, $body, $result_code);

                return $results;
            }
            $chArr[$idx] = $this->createUploadHandle($url, $filePathArray[$idx]);

            curl_multi_add_handle($mh, $chArr[$idx]);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        // Get content and remove handles.
        $body = '';
        for ($idx = 0; $idx < count($chArr); $idx++) {
            $ch = $chArr[$idx];

            $results[$idx] = curl_multi_getcontent($ch);

            // if request failed.
            if (!$results[$idx]) {
                $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $body                = curl_error($ch);

                //The server successfully processed the request, but is not returning any content.
                if ($this->http_response == 204) {
                    continue;
                }

                // HostNotFound, No route to Host, etc Network error
                $result_code = -1;
                $body        = 'CURL Error: = ' . $body;
                $this->log->addError($body);
            } else {
                // if request was ok, parsing http response code.
                $result_code = $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
                if ($this->http_response != 200 && $this->http_response != 201) {
                    $body = 'CURL HTTP Request Failed: Status Code : '
                            . $this->http_response . ', URL:' . $url;

                    $this->log->addError($body);
                }
            }
        }

        $this->closeCURLHandle($chArr, $mh, $body, $result_code);

        return $results;
    }

    /**
     * @param array $chArr
     * @param       $mh
     * @param       $body
     * @param       $result_code
     *
     * @throws \JiraRestApi\JiraException
     */
    protected function closeCURLHandle(array $chArr, $mh, $body, $result_code)
    {
        foreach ($chArr as $ch) {
            $this->log->addDebug('CURL Close handle..');
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        $this->log->addDebug('CURL Multi Close handle..');
        curl_multi_close($mh);
        if ($result_code != 200) {
            // @TODO $body might have not been defined
            throw new JiraException('CURL Error: = ' . $body, $result_code);
        }
    }

    /**
     * Get URL by context.
     *
     * @param string $context
     *
     * @return string
     */
    protected function createUrlByContext($context)
    {
        $host = $this->getConfiguration()->getJiraHost();

        return $host . $this->api_uri . '/' . preg_replace('/\//', '', $context, 1);
    }

    /**
     * Add authorize to curl request.
     *
     * @param resource $ch
     */
    protected function authorization($ch)
    {
        // use cookie
        if ($this->getConfiguration()->isCookieAuthorizationEnabled()) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);

            $this->log->addDebug('Using cookie..');
        }

        // if cookie file not exist, using id/pwd login
        if (!file_exists($this->cookie)) {
            $username = $this->getConfiguration()->getJiraUser();
            $password = $this->getConfiguration()->getJiraPassword();
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        }
    }

    /**
     * Jira Rest API Configuration.
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set a custom Jira API URI for the request.
     *
     * @param string $api_uri
     */
    public function setAPIUri($api_uri)
    {
        $this->api_uri = $api_uri;
    }

    /**
     * convert to query array to http query parameter.
     *
     * @param $paramArray
     *
     * @return string
     */
    public function toHttpQueryParameter($paramArray)
    {
        $queryParam = '?';

        foreach ($paramArray as $key => $value) {
            $v = null;

            // some param field(Ex: expand) type is array.
            if (is_array($value)) {
                $v = implode(',', $value);
            } else {
                $v = $value;
            }

            $queryParam .= $key . '=' . $v . '&';
        }

        return $queryParam;
    }

    /**
     * download and save into outDir.
     *
     * @param $url    full url
     * @param $outDir save dir
     * @param $file   save filename
     *
     * @throws JiraException
     *
     * @return bool|mixed
     */
    public function download($url, $outDir, $file)
    {
        $file = fopen($outDir . DIRECTORY_SEPARATOR . $file, 'w');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // output to file handle
        curl_setopt($ch, CURLOPT_FILE, $file);

        $this->authorization($ch);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());

        // curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER,
            ['Accept: */*', 'Content-Type: application/json', 'X-Atlassian-Token: no-check']);

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        $this->log->addDebug('Curl exec=' . $url);
        $response = curl_exec($ch);

        // if request failed.
        if (!$response) {
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body                = curl_error($ch);
            curl_close($ch);
            fclose($file);

            /*
             * 201: The request has been fulfilled, resulting in the creation of a new resource.
             * 204: The server successfully processed the request, but is not returning any content.
             */
            if ($this->http_response === 204 || $this->http_response === 201) {
                return true;
            }

            // HostNotFound, No route to Host, etc Network error
            $msg = sprintf('CURL Error: http response=%d, %s', $this->http_response, $body);

            $this->log->addError($msg);

            throw new JiraException($msg);
        } else {
            // if request was ok, parsing http response code.
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            fclose($file);

            // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
            if ($this->http_response != 200 && $this->http_response != 201) {
                throw new JiraException('CURL HTTP Request Failed: Status Code : '
                                        . $this->http_response . ', URL:' . $url
                                        . "\nError Message : " . $response, $this->http_response);
            }
        }

        return $response;
    }
}
